<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Domain\CarUpload\Services\BulkCarImportService;
use App\Domain\CarUpload\Services\BulkCarValidatorService;
use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Infrastructure\Eloquent\User\StkBulkUploadBatch;

class BulkCarUploadPage extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort  = NavigationSort::BULK_CAR_UPLOAD->value;
    protected static ?string $title           = '車両一括登録';
    protected static string  $view            = 'filament.pages.bulk-car-upload';

    // dealer/dealer_staffのみアクセス可能
    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['dealer', 'dealer_staff']);
    }

    // ===== State =====

    /** @var string upload=アップロード / history=履歴 */
    public string $activeTab = 'upload';

    /** バッチ履歴一覧 */
    public array $batches = [];

    /** @var string STEP1=xlsx選択 / STEP2=画像アップロード / STEP3=確認・承認依頼 */
    public string $step = 'STEP1';

    /** xlsxバリデーションエラー一覧 */
    public array $xlsxErrors = [];

    /** 画像バリデーションエラー一覧 */
    public array $imageErrors = [];

    /** バリデーション済み行データ（一時保持） */
    public array $validatedRows = [];

    /** プレビュー用件数サマリー */
    public array $previewSummary = [];

    /** アップロード済み画像のS3パス一覧 ['フォルダ名' => ['パス1', ...]] */
    public array $uploadedImageMap = [];

    /** 登録済み車両IDリスト（承認依頼用） */
    public array $importedCarIds = [];

    /** チャンクアップロード進捗 */
    public int $imageUploadCurrent = 0;

    // ===== mount =====

    public function mount(): void
    {
        $this->loadBatches();
    }

    // ===== STEP1: xlsxアップロード・バリデーション =====

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
            $this->step           = 'STEP2';

            Notification::make()
                ->title('xlsxのバリデーションが完了しました。次に画像をアップロードしてください。')
                ->success()
                ->send();

        } catch (\Throwable $e) {
            $this->xlsxErrors = [['row' => '-', 'column' => '-', 'message' => 'ファイルの読み込みに失敗しました：' . $e->getMessage()]];
        }
    }

    // ===== STEP2: 画像チャンクアップロード =====

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

        $this->step = 'STEP3';

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

    // ===== STEP3: データ登録・一括承認依頼 =====

    public function importAndRequestApproval(): void
    {
        $dealerId = Auth::user()->dealer_id;

        $carIds = app(BulkCarImportService::class)->import(
            rows:       $this->validatedRows,
            dealerId:   $dealerId,
            imageMap:   $this->uploadedImageMap,
            onProgress: function (int $current, int $total) {
                $this->dispatch('import-progress', current: $current, total: $total);
            }
        );

        if (!empty($carIds)) {
            app(BulkCarImportService::class)->requestApproval($carIds, $dealerId);
        }

        $this->importedCarIds = $carIds;
        $this->step           = 'DONE';

        $this->loadBatches();

        Notification::make()
            ->title(count($carIds) . '台の車両を登録し、承認依頼を送信しました。')
            ->success()
            ->send();
    }

    // ===== ユーティリティ =====

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        if ($tab === 'history') {
            $this->loadBatches();
        }
    }

    private function loadBatches(): void
    {
        $dealerId = Auth::user()->dealer_id;

        $this->batches = StkBulkUploadBatch::where('dealer_id', $dealerId)
            ->with('cars')
            ->orderBy('uploaded_at', 'desc')
            ->get()
            ->map(fn ($batch) => [
                'id'             => $batch->id,
                'uploaded_at'    => $batch->uploaded_at?->format('Y/m/d H:i'),
                'total_count'    => $batch->total_count,
                'create_count'   => $batch->create_count,
                'update_count'   => $batch->update_count,
                'delete_count'   => $batch->delete_count,
                'approved_count' => $batch->approved_count,
                'rejected_count' => $batch->rejected_count,
                'pending_count'  => $batch->pending_count,
                'status'         => $batch->status,
                'status_label'   => $batch->status_label,
                'detail_url'     => BulkCarBatchDetailPage::getUrl(['batchId' => $batch->id]),
            ])
            ->toArray();
    }

    // ===== 完了マーク =====
    public function resolveImage(int $imageId): void
    {
        $car = $this->getCurrentCar();
        if (!$car || !$car->rejection_reason) return;

        $reason = $car->rejection_reason;
        $reason['flagged_images'] = collect($reason['flagged_images'] ?? [])
            ->map(fn ($img) => $img['id'] === $imageId ? array_merge($img, ['resolved' => true]) : $img)
            ->toArray();

        $car->update(['rejection_reason' => $reason]);
        $this->record->load(['cars.series', 'cars.detail', 'cars.images', 'cars.options', 'dealer']);
        $this->syncRejectionState();

        Notification::make()->title('画像指摘を完了にしました')->success()->send();
    }

    public function resolveItem(int $index): void
    {
        $car = $this->getCurrentCar();
        if (!$car || !$car->rejection_reason) return;

        $reason = $car->rejection_reason;
        $items  = $reason['items'] ?? [];
        if (isset($items[$index])) {
            $items[$index]['resolved'] = true;
        }
        $reason['items'] = $items;

        $car->update(['rejection_reason' => $reason]);
        $this->record->load(['cars.series', 'cars.detail', 'cars.images', 'cars.options', 'dealer']);
        $this->syncRejectionState();

        Notification::make()->title('項目指摘を完了にしました')->success()->send();
    }

    private function syncRejectionState(): void
    {
        foreach ($this->record->cars as $car) {
            if ($car->rejection_reason && is_array($car->rejection_reason)) {
                $this->generalComments[$car->id] = $car->rejection_reason['general_comment'] ?? '';
                $this->flaggedImages[$car->id]   = $car->rejection_reason['flagged_images'] ?? [];
                $this->rejectionItems[$car->id]  = $car->rejection_reason['items'] ?? [];
            }
        }
    }
}