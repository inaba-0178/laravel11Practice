<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Domain\CarUpload\Services\BulkCarImportService;
use App\Domain\CarUpload\Services\BulkCarValidatorService;
use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Livewire\Attributes\State;

class BulkCarUploadPage extends Page
{
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

    /** 一時xlsxファイルパス */
    public ?string $tempXlsxPath = null;

    /** チャンクアップロード進捗 */
    public int $imageUploadTotal   = 0;
    public int $imageUploadCurrent = 0;

    // ===== STEP1: xlsxアップロード・バリデーション =====

    /**
     * xlsxファイルを受け取りバリデーション
     * Livewireのファイルアップロード経由で呼ばれる
     */
    public function uploadXlsx(string $base64Data, string $filename): void
    {
        $this->xlsxErrors    = [];
        $this->validatedRows = [];

        try {
            \Log::info('uploadXlsx開始', [
                'filename'       => $filename,
                'base64_length'  => strlen($base64Data),
                'base64_preview' => substr($base64Data, 0, 50),
            ]);

            $decoded = base64_decode($base64Data, true);

            \Log::info('デコード結果', [
                'decoded' => $decoded === false ? 'false' : strlen($decoded) . 'bytes',
            ]);

            if ($decoded === false) {
                $this->xlsxErrors = [['row' => '-', 'column' => '-', 'message' => 'Base64デコードに失敗しました。']];
                return;
            }

            $tmpPath = storage_path('app/private/bulk-uploads/tmp_' . uniqid() . '.xlsx');
            $result  = file_put_contents($tmpPath, $decoded);

            \Log::info('file_put_contents結果', [
                'tmpPath' => $tmpPath,
                'result'  => $result,
            ]);

            \Log::info('IOFactory::load開始');
$spreadsheet = IOFactory::load($tmpPath);
\Log::info('IOFactory::load完了');

$sheet = $spreadsheet->getActiveSheet();
\Log::info('getActiveSheet完了');

$rows = $this->parseSheet($sheet);
\Log::info('parseSheet完了', ['rows_count' => count($rows)]);

            if ($result === false) {
                $this->xlsxErrors = [['row' => '-', 'column' => '-', 'message' => 'ファイルの一時保存に失敗しました。']];
                return;
            }

            $spreadsheet = IOFactory::load($tmpPath);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $this->parseSheet($sheet);

            @unlink($tmpPath);

            if (empty($rows)) {
                $this->xlsxErrors = [['row' => '-', 'column' => '-', 'message' => 'データが1件もありません。']];
                return;
            }

            \Log::info('parseSheet完了', ['rows_count' => count($rows)]);

// ↓ここから追加
if (empty($rows)) {
    \Log::info('rows空のため終了');
    $this->xlsxErrors = [['row' => '-', 'column' => '-', 'message' => 'データが1件もありません。']];
    return;
}

$dealerId = Auth::user()->dealer_id;
\Log::info('バリデーション開始', ['dealerId' => $dealerId]);

$result = app(BulkCarValidatorService::class)->validate($rows, $dealerId);
\Log::info('バリデーション完了', ['valid' => $result['valid'], 'errors' => $result['errors']]);
// ↑ここまで追加

            if (!$result['valid']) {
                $this->xlsxErrors = $result['errors'];
                return;
            }

            $this->validatedRows  = $rows;
            $this->tempXlsxPath   = $tmpPath;
            $this->previewSummary = $this->buildPreviewSummary($rows);
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

    /**
     * 画像チャンクを受け取りS3に保存
     * フロントから10枚単位でPOSTされる
     */
    public function uploadImageChunk(array $files): void
    {
        \Log::info('uploadImageChunk開始', ['files_count' => count($files)]);

        $dealerId = Auth::user()->dealer_id;

        foreach ($files as $file) {
            $filename     = $file['name'];
            $base64       = $file['base64'];
            $relativePath = $file['relativePath'] ?? '';

            \Log::info('画像処理中', [
                'filename'     => $filename,
                'relativePath' => $relativePath,
            ]);

            // bulkCar/car1/001.jpg → car1
            $parts      = explode('/', $relativePath);
            $folderName = $parts[1] ?? pathinfo($filename, PATHINFO_FILENAME);

            $decoded = base64_decode($base64, true);
            if ($decoded === false) {
                \Log::error('画像デコード失敗', ['filename' => $filename]);
                continue;
            }

            $s3Path = "dealers/{$dealerId}/cars/{$folderName}/{$filename}";
            Storage::disk('s3')->put($s3Path, $decoded);

            \Log::info('S3アップロード完了', ['s3Path' => $s3Path]);

            $this->uploadedImageMap[$folderName][] = $s3Path;
            $this->imageUploadCurrent++;
        }

        \Log::info('uploadImageChunk完了', ['imageUploadCurrent' => $this->imageUploadCurrent]);
    }

    /**
     * 画像アップロード完了後のバリデーション
     */
    public function validateImages(): void
    {
        $this->imageErrors = [];

        // uploadedImageMapのキー（フォルダ名）一覧をそのまま渡す
        $uploadedFilePaths = [];
        foreach ($this->uploadedImageMap as $folder => $paths) {
            foreach ($paths as $path) {
                $uploadedFilePaths[] = $path;
            }
        }

        $result = app(BulkCarValidatorService::class)->validateImages(
            $this->validatedRows,
            $uploadedFilePaths
        );

        if (!$result['valid']) {
            $this->imageErrors = $result['errors'];

            // アップロード済み画像を削除してやり直し
            $this->cleanupUploadedImages();
            return;
        }

        $this->step = 'STEP3';

        Notification::make()
            ->title('画像のバリデーションが完了しました。内容を確認して承認依頼を送ってください。')
            ->success()
            ->send();
    }

    /**
     * 再アップロード時に前回の画像をS3から削除
     */
    public function resetImageUpload(): void
    {
        $this->cleanupUploadedImages();
        $this->uploadedImageMap    = [];
        $this->imageUploadCurrent  = 0;
        $this->imageErrors         = [];
    }

    // ===== STEP3: データ登録・一括承認依頼 =====

    /**
     * 車両データ一括登録 → 承認依頼
     */
    public function importAndRequestApproval(): void
    {
        \Log::info('importAndRequestApproval開始', [
            'uploadedImageMap' => $this->uploadedImageMap,
            'validatedRows_count' => count($this->validatedRows),
        ]);

        $dealerId = Auth::user()->dealer_id;

        // 一括登録（操作列で分岐）
        $carIds = app(BulkCarImportService::class)->import(
            rows:       $this->validatedRows,
            dealerId:   $dealerId,
            imageMap:   $this->uploadedImageMap,
            onProgress: function (int $current, int $total) {
                $this->dispatch('import-progress', current: $current, total: $total);
            }
        );

        // 承認依頼（削除のみの場合はcarIdsが空の可能性あり）
        if (!empty($carIds)) {
            app(BulkCarImportService::class)->requestApproval($carIds, $dealerId);
        }

        $this->importedCarIds = $carIds;
        $this->step           = 'DONE';

        Notification::make()
            ->title(count($carIds) . '台の車両を登録し、承認依頼を送信しました。')
            ->success()
            ->send();
    }

    // ===== ユーティリティ =====

    /**
     * PhpSpreadsheetのシートを連想配列に変換
     */
    private function parseSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        $data    = $sheet->toArray();
        $headers = $data[1] ?? []; // 2行目がヘッダー（1行目はカテゴリグループ）

        // ★を除去してヘッダー名を正規化
        $headers = array_map(fn ($h) => ltrim(trim((string)$h), '★'), $headers);

        $rows = [];
        for ($i = 2; $i < count($data); $i++) {
            $row = $data[$i];
            // 空行スキップ
            if (empty(array_filter($row, fn ($v) => !empty(trim((string)$v))))) {
                continue;
            }
            $rows[] = array_combine($headers, array_pad($row, count($headers), null));
        }

        return $rows;
    }

    /**
     * プレビュー用サマリー生成
     */
    private function buildPreviewSummary(array $rows): array
    {
        return [
            'total'       => count($rows),
            'folders'     => collect($rows)->pluck('画像フォルダ名')->filter()->unique()->count(),
            'unique_ids'  => collect($rows)->pluck('ユニークID')->filter()->unique()->count(),
        ];
    }

    /**
     * アップロード済みファイル名一覧を取得
     */
    private function getAllUploadedFilenames(): array
    {
        $filenames = [];
        foreach ($this->uploadedImageMap as $folder => $paths) {
            foreach ($paths as $path) {
                $filenames[] = basename($path);
            }
        }
        return $filenames;
    }

    /**
     * S3にアップロード済みの画像を削除
     */
    private function cleanupUploadedImages(): void
    {
        foreach ($this->uploadedImageMap as $folder => $paths) {
            foreach ($paths as $path) {
                Storage::disk('s3')->delete($path);
            }
        }
    }
}