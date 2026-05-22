<?php

declare(strict_types=1);

namespace App\Domain\CarUpload\Services;

use App\Domain\CarUpload\DataTransformers\BulkCarRowTransformer;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkCarDetails;
use App\Infrastructure\Eloquent\User\StkCarOptions;
use App\Constants\CarStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Constants\FileStatus;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use App\Infrastructure\Eloquent\User\StkCarImages;
use App\Infrastructure\Eloquent\User\StkBulkUploadBatch;
use Illuminate\Support\Facades\Auth;
use App\Domain\CarUpload\Notifications\BulkCarRegistrationPendingNotification;

class BulkCarImportService
{
    public function __construct(
        private readonly BulkCarRowTransformer $transformer,
    ) {}

    /**
     * xlsx行データを一括登録してpendingに変更
     *
     * @param  array         $rows      バリデーション済み行データ
     * @param  int           $dealerId
     * @param  array         $imageMap  ['フォルダ名' => ['s3パス1', 's3パス2', ...]]
     * @param  callable|null $onProgress
     * @return array 登録されたStkCarのID一覧
     */
    public function import(array $rows, int $dealerId, array $imageMap = [], ?callable $onProgress = null): array
    {
        $carIds = [];

        DB::connection('user')->transaction(function () use ($rows, $dealerId, $imageMap, $onProgress, &$carIds) {

            // 操作ごとの件数を集計
            $createCount = collect($rows)->filter(fn ($r) => trim($r['操作'] ?? '') === FileStatus::LABELS[FileStatus::CREATE])->count();
            $updateCount = collect($rows)->filter(fn ($r) => trim($r['操作'] ?? '') === FileStatus::LABELS[FileStatus::UPDATE])->count();
            $deleteCount = collect($rows)->filter(fn ($r) => trim($r['操作'] ?? '') === FileStatus::LABELS[FileStatus::DELETE])->count();

            // バッチ作成
            $batch = StkBulkUploadBatch::create([
                'dealer_id'     => $dealerId,
                'uploaded_by'   => Auth::id(),
                'uploaded_at'   => now(),
                'total_count'   => count($rows),
                'create_count'  => $createCount,
                'update_count'  => $updateCount,
                'delete_count'  => $deleteCount,
                'pending_count' => $createCount + $updateCount,
            ]);

            foreach ($rows as $index => $row) {
                $operation = trim($row['操作'] ?? '');
                $uniqueKey = trim($row['ユニークID'] ?? '');

                // ===== 削除 =====
                if ($operation === FileStatus::LABELS[FileStatus::DELETE]) {
                    $this->deleteCar($uniqueKey, $dealerId);

                // ===== 更新 =====
                } elseif ($operation === FileStatus::LABELS[FileStatus::UPDATE]) {
                    $existing = StkCar::where('bulk_upload_key', $uniqueKey)
                        ->where('dealer_id', $dealerId)
                        ->first();

                    if ($existing) {
                        if ($existing->status === CarStatus::RESERVED) {
                            continue;
                        }
                        $this->deleteCarWithImages($existing);
                    }
                    $car              = $this->importRow($row, $dealerId, $imageMap);
                    $car->bulk_batch_id = $batch->id;
                    $car->save();
                    $carIds[]         = $car->id;

                // ===== 新規 =====
                } else {
                    $car              = $this->importRow($row, $dealerId, $imageMap);
                    $car->bulk_batch_id = $batch->id;
                    $car->save();
                    $carIds[]         = $car->id;
                }

                if ($onProgress) {
                    $onProgress($index + 1, count($rows));
                }
            }
        });

        return $carIds;
    }

    /**
     * 車両・DBレコード・S3フォルダを削除
     */
    private function deleteCarWithImages(StkCar $car): void
    {
        $car->images()->delete();
        $car->delete();
    }

    /**
     * ユニークキーで車両を削除
     */
    private function deleteCar(string $uniqueKey, int $dealerId): void
    {
        $car = StkCar::where('bulk_upload_key', $uniqueKey)
            ->where('dealer_id', $dealerId)
            ->first();

        if ($car) {
            // 削除の場合はS3も削除
            $car->images()->each(function ($image) {
                if ($image->image_url) {
                    Storage::disk('s3')->delete($image->image_url);
                }
            });
            $car->images()->delete();
            $car->delete();
        }
    }

    /**
     * 一括承認依頼（全車両をpendingに変更して通知）
     */
    public function requestApproval(array $carIds, int $dealerId): void
    {
        DB::connection('user')->transaction(function () use ($carIds, $dealerId) {
            StkCar::whereIn('id', $carIds)
                ->where('dealer_id', $dealerId)
                ->whereIn('status', [CarStatus::DRAFT])
                ->update([
                    'status'     => CarStatus::PENDING,
                    'updated_at' => now(),
                ]);
        });

        // 管理者へ通知
        $dealer     = StkCarDealer::find($dealerId);
        $dealerName = $dealer?->name ?? '不明';
        $count      = count($carIds);

        User::whereIn('role', ['super', 'admin'])
            ->where('is_active', 1)
            ->get()
            ->each(fn (User $u) => $u->notify(
                new BulkCarRegistrationPendingNotification(
                    dealerName: $dealerName,
                    carCount:   $count,
                    carIds:     $carIds,
                )
            ));
    }

    /**
     * 1行分の登録処理
     */
    private function importRow(array $row, int $dealerId, array $imageMap): StkCar
    {
        // stk_cars登録
        $carData = $this->transformer->transformCarData($row, $dealerId);
        $car     = StkCar::create($carData);

        // stk_car_details登録
        $detailData           = $this->transformer->transformDetailData($row);
        $detailData['car_id'] = $car->id;
        StkCarDetails::create($detailData);

        // stk_car_options登録
        $options = $this->transformer->transformOptionsData($row);
        foreach ($options as $option) {
            StkCarOptions::create(array_merge($option, ['car_id' => $car->id, 'is_equipped' => 1]));
        }

        // 画像登録
        $folderName = trim($row['画像フォルダ名'] ?? '');
        if (!empty($folderName) && isset($imageMap[$folderName])) {
            $this->attachImages($car, $imageMap[$folderName]);
        }

        return $car;
    }

    /**
     * 画像をstk_car_imagesに登録
     * ファイル名の連番順でdisplay_orderをセット
     * 001が自動的にメイン画像
     */
    private function attachImages(StkCar $car, array $s3Paths): void
    {
        usort($s3Paths, function ($a, $b) {
            $aNum = $this->extractSerialNumber($a);
            $bNum = $this->extractSerialNumber($b);
            return $aNum <=> $bNum;
        });

        $mainImageSet = false;
        foreach ($s3Paths as $order => $s3Path) {
            $isMain = !$mainImageSet;

            StkCarImages::create([
                'car_id'        => $car->id,
                'image_url'     => $s3Path,
                'image_type'    => 'exterior',
                'display_order' => $order + 1,
                'is_main'       => $isMain ? 1 : 0,
            ]);

            if ($isMain) {
                $car->update(['main_image_url' => $s3Path]);
                $mainImageSet = true;
            }
        }
    }

    /**
     * ファイル名から連番を抽出
     * 例: car1_001.jpg → 1
     */
    private function extractSerialNumber(string $path): int
    {
        $basename = pathinfo($path, PATHINFO_FILENAME);
        $parts    = explode('_', $basename);
        $serial   = end($parts);
        return is_numeric($serial) ? (int)$serial : 0;
    }

    /**
     * バッチのカウントを更新
     * 承認・差し戻し時に呼ぶ
     */
    public function updateBatchCounts(int $batchId): void
    {
        $batch = StkBulkUploadBatch::find($batchId);
        if (!$batch) return;

        $cars = StkCar::where('bulk_batch_id', $batchId)
            ->withTrashed()
            ->get();

        $approvedCount = $cars->filter(fn ($c) => in_array($c->status, [
            CarStatus::AVAILABLE,
            CarStatus::APPROVED_PENDING,
        ]))->count();
        $rejectedCount = $cars->filter(fn ($c) => $c->status === CarStatus::REJECTED)->count();
        $pendingCount  = $cars->filter(fn ($c) => $c->status === CarStatus::PENDING)->count();

        $batch->update([
            'approved_count' => $approvedCount,
            'rejected_count' => $rejectedCount,
            'pending_count'  => $pendingCount,
            'approved_at'    => $pendingCount === 0 && $rejectedCount === 0 ? now() : null,
        ]);
    }
}