<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\CarStatus;
use App\Domain\CarUpload\Services\BulkCarImportService;
use App\Domain\CarUpload\Services\BulkCarValidatorService;
use App\Infrastructure\Eloquent\User\StkBulkUploadBatch;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkCarImages;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BulkCarBatchDetailPage extends Page
{
    use HasResourcePermission;
    protected static string $view = 'filament.pages.bulk-car-batch-detail';
    protected static bool   $shouldRegisterNavigation = false;

    public StkBulkUploadBatch $record;
    public int   $currentCarId = 0;

    /** 対応内容（車両IDをキーにした配列） */
    public array $generalResponses = [];
    public array $imageResponses   = [];
    public array $itemResponses    = [];

    // ===== 再アップロード用State =====

    /** @var string STEP1=xlsx選択 / STEP2=画像アップロード / STEP3=確認・承認依頼 / DONE=完了 */
    public string $uploadStep = 'STEP1';

    /** xlsxバリデーションエラー一覧 */
    public array $xlsxErrors = [];

    /** 画像バリデーションエラー一覧 */
    public array $imageErrors = [];

    /** バリデーション済み行データ */
    public array $validatedRows = [];

    /** プレビュー用件数サマリー */
    public array $previewSummary = [];

    /** アップロード済み画像のS3パス一覧 */
    public array $uploadedImageMap = [];

    /** 登録済み車両IDリスト */
    public array $importedCarIds = [];

    /** チャンクアップロード進捗 */
    public int $imageUploadCurrent = 0;

    public function mount(): void
    {
        $batchId = request()->query('batchId');

        if (!$batchId) {
            abort(404);
        }

        $batch = StkBulkUploadBatch::with([
            'cars.series',
            'cars.detail',
            'cars.images',
            'cars.options',
            'dealer',
        ])->findOrFail($batchId);

        abort_unless(
            $batch->dealer_id === Auth::user()->dealer_id,
            403
        );

        $this->record = $batch;

        $firstCar = $batch->cars->where('status', CarStatus::REJECTED)->first()
            ?? $batch->cars->first();

        if ($firstCar) {
            $this->currentCarId = $firstCar->id;
        }

        foreach ($batch->cars as $car) {
            $reason = $car->rejection_reason ?? [];
            $this->generalResponses[$car->id] = $reason['general_response'] ?? '';
            $this->imageResponses[$car->id]   = collect($reason['flagged_images'] ?? [])
                ->mapWithKeys(fn ($img) => [$img['id'] => $img['dealer_response'] ?? ''])
                ->toArray();
            $this->itemResponses[$car->id] = collect($reason['items'] ?? [])
                ->map(fn ($item) => $item['dealer_response'] ?? '')
                ->values()
                ->toArray();
        }
    }

    public function getTitle(): string
    {
        return 'バッチ詳細 #' . $this->record->id;
    }

    // ===== タブ切り替え =====
    public function selectCar(int $carId): void
    {
        $this->currentCarId = $carId;
    }

    public function getCurrentCar(): ?StkCar
    {
        return $this->record->cars->firstWhere('id', $this->currentCarId);
    }

    // ===== 対応内容保存 =====
    public function saveDealerResponse(): void
    {
        $car = $this->getCurrentCar();
        if (!$car || !$car->rejection_reason) return;

        $carId  = $car->id;
        $reason = $car->rejection_reason;

        $reason['general_response'] = $this->generalResponses[$carId] ?? '';

        $imageResponses = $this->imageResponses[$carId] ?? [];
        $reason['flagged_images'] = collect($reason['flagged_images'] ?? [])
            ->map(function ($img) use ($imageResponses) {
                if (isset($imageResponses[$img['id']])) {
                    $img['dealer_response'] = $imageResponses[$img['id']];
                }
                return $img;
            })
            ->toArray();

        $itemResponses = $this->itemResponses[$carId] ?? [];
        $reason['items'] = collect($reason['items'] ?? [])
            ->map(function ($item, $idx) use ($itemResponses) {
                if (isset($itemResponses[$idx])) {
                    $item['dealer_response'] = $itemResponses[$idx];
                }
                return $item;
            })
            ->toArray();

        $car->update(['rejection_reason' => $reason]);
        $this->record->load(['cars.series', 'cars.detail', 'cars.images', 'cars.options', 'dealer']);

        Notification::make()->title('対応内容を保存しました')->success()->send();
    }

    // ===== 再アップロード: STEP1 =====
    public function uploadXlsx(string $base64Data, string $filename): void
    {
        $this->xlsxErrors    = [];
        $this->validatedRows = [];

        try {
            $decoded = base64_decode($base64Data, true);

            if ($decoded === false) {
                $this->xlsxErrors = [['row' => '-', 'column' => '-', 'message' => 'Base64デコードに失敗しました。']];
                return;
            }

            $tmpPath = storage_path('app/private/bulk-uploads/tmp_' . uniqid() . '.xlsx');
            $result  = file_put_contents($tmpPath, $decoded);

            if ($result === false) {
                $this->xlsxErrors = [['row' => '-', 'column' => '-', 'message' => 'ファイルの一時保存に失敗しました。']];
                return;
            }

            $spreadsheet = IOFactory::load($tmpPath);
            $rows        = app(BulkCarImportService::class)->parseSheet($spreadsheet->getActiveSheet());

            @unlink($tmpPath);

            if (empty($rows)) {
                $this->xlsxErrors = [['row' => '-', 'column' => '-', 'message' => 'データが1件もありません。']];
                return;
            }

            $dealerId = Auth::user()->dealer_id;
            $result   = app(BulkCarValidatorService::class)->validate($rows, $dealerId);

            if (!$result['valid']) {
                $this->xlsxErrors = $result['errors'];
                return;
            }

            $this->validatedRows  = $rows;
            $this->previewSummary = app(BulkCarImportService::class)->buildPreviewSummary($rows);
            $this->uploadStep     = 'STEP2';

            Notification::make()
                ->title('xlsxのバリデーションが完了しました。次に画像をアップロードしてください。')
                ->success()
                ->send();

        } catch (\Throwable $e) {
            $this->xlsxErrors = [['row' => '-', 'column' => '-', 'message' => 'ファイルの読み込みに失敗しました：' . $e->getMessage()]];
        }
    }

    // ===== 再アップロード: STEP2 =====
    public function uploadImageChunk(array $files): void
    {
        $dealerId = Auth::user()->dealer_id;

        foreach ($files as $file) {
            $filename     = $file['name'];
            $base64       = $file['base64'];
            $relativePath = $file['relativePath'] ?? '';

            $parts      = explode('/', $relativePath);
            $folderName = $parts[1] ?? pathinfo($filename, PATHINFO_FILENAME);

            $decoded = base64_decode($base64, true);
            if ($decoded === false) continue;

            $s3Path = "dealers/{$dealerId}/cars/{$folderName}/{$filename}";
            Storage::disk('s3')->put($s3Path, $decoded);

            $this->uploadedImageMap[$folderName][] = $s3Path;
            $this->imageUploadCurrent++;
        }
    }

    public function validateImages(): void
    {
        $this->imageErrors = [];

        $uploadedFilePaths = collect($this->uploadedImageMap)->flatten()->toArray();

        $result = app(BulkCarValidatorService::class)->validateImages(
            $this->validatedRows,
            $uploadedFilePaths
        );

        if (!$result['valid']) {
            $this->imageErrors = $result['errors'];
            app(BulkCarImportService::class)->cleanupUploadedImages($this->uploadedImageMap);
            return;
        }

        $this->uploadStep = 'STEP3';

        Notification::make()
            ->title('画像のバリデーションが完了しました。内容を確認して承認依頼を送ってください。')
            ->success()
            ->send();
    }

    public function resetImageUpload(): void
    {
        app(BulkCarImportService::class)->cleanupUploadedImages($this->uploadedImageMap);
        $this->uploadedImageMap   = [];
        $this->imageUploadCurrent = 0;
        $this->imageErrors        = [];
    }

    // ===== 再アップロード: STEP3 =====
    public function importAndRequestApproval(): void
    {
        $dealerId = Auth::user()->dealer_id;

        $carIds = app(BulkCarImportService::class)->import(
            rows:            $this->validatedRows,
            dealerId:        $dealerId,
            imageMap:        $this->uploadedImageMap,
            onProgress:      function (int $current, int $total) {
                $this->dispatch('import-progress', current: $current, total: $total);
            },
            existingBatchId: $this->record->id, // ← 既存バッチIDを渡す
        );

        if (!empty($carIds)) {
            app(BulkCarImportService::class)->requestApproval($carIds, $dealerId);
        }

        $this->importedCarIds = $carIds;
        $this->uploadStep     = 'DONE';

        $this->record = StkBulkUploadBatch::with([
            'cars.series',
            'cars.detail',
            'cars.images',
            'cars.options',
            'dealer',
        ])->findOrFail($this->record->id);

        Notification::make()
            ->title(count($carIds) . '台の車両を登録し、承認依頼を送信しました。')
            ->success()
            ->send();
    }

    public function resetUpload(): void
    {
        $this->uploadStep         = 'STEP1';
        $this->xlsxErrors         = [];
        $this->imageErrors        = [];
        $this->validatedRows      = [];
        $this->previewSummary     = [];
        $this->uploadedImageMap   = [];
        $this->importedCarIds     = [];
        $this->imageUploadCurrent = 0;
    }

    // ===== 表示用ヘルパー =====
    public function getImages(): array
    {
        $car = $this->getCurrentCar();
        if (!$car) return [];

        return StkCarImages::where('car_id', $car->id)
            ->orderBy('display_order')
            ->get()
            ->map(fn ($img) => [
                'id'      => $img->id,
                'url'     => Storage::disk('s3')->url($img->image_url),
                'is_main' => $img->is_main,
            ])
            ->toArray();
    }

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        return route('filament.admin.pages.bulk-car-batch-detail-page', $parameters, $isAbsolute);
    }

    protected static function getParentPermissionKey(): ?string
    {
        return 'BulkCarApprovalResource';
    }

}