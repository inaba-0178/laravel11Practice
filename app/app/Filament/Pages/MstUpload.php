<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Constants\Role\RoleManagement;
use App\Constants\MstTableMap;
use App\Domain\Mst\Services\MstImportService;
use App\Domain\Mst\Services\MstValidatorService;
use App\Infrastructure\Eloquent\Mst\MstVersion;
use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MstUpload extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = NavigationGroup::MST_UPDATE_GROUP->value;
    protected static ?int    $navigationSort  = NavigationSort::MST_UPLOAD->value;
    protected static ?string $title           = 'マスタアップロード';
    protected static string  $view            = 'filament.pages.mst-upload';


    // ===== STEPフロー =====
    // STEP1: xlsxアップロード・バリデーション
    // STEP2: 画像フォルダアップロード・チェック
    // STEP3: 確認・データ投入
    // DONE:  完了

    public string  $step             = 'STEP1';

    // STEP1
    public ?string $uploadedFilePath = null;
    public array   $xlsxErrors       = [];
    public array   $sheets           = [];
    public string  $version          = '';
    public string  $description      = '';
    public string  $versionType      = 'patch';
    public array   $previewCounts    = [];

    // STEP2
    // uploadedImageMap: ['mst_manufacturer_images' => ['toyota.jpg', 'honda.png'], ...]
    public array   $uploadedImageMap  = [];
    public array   $imageErrors       = [];
    public int     $imageUploadCount  = 0;

    // STEP3
    public bool    $isImporting      = false;
    public bool    $importCompleted  = false;

    public function mount(): void
    {
        $this->version = MstVersion::generateNextVersion('patch');
    }

    // ===== STEP1: xlsxアップロード・バリデーション =====

    public function uploadAndValidate(): void
    {
        $this->validate([
            'uploadedFilePath' => 'required',
        ], [
            'uploadedFilePath.required' => 'ファイルを選択してください',
        ]);

        $this->xlsxErrors   = [];
        $this->previewCounts = [];

        try {
            $this->sheets     = $this->readSpreadsheet($this->uploadedFilePath);
            $validator        = new MstValidatorService();
            // STEP2での画像チェックのため、ここでは画像バリデーションをスキップ
            $this->xlsxErrors = $validator->validateWithoutImages($this->sheets);

            if (empty($this->xlsxErrors)) {
                $importer            = new MstImportService();
                $this->previewCounts = $importer->getPreviewCounts($this->sheets);
                $this->version       = MstVersion::generateNextVersion($this->versionType);

                // 画像カラムを持つシートが1つでもあれば STEP2 へ、なければ STEP3 へ
                if ($this->hasImageSheets()) {
                    $this->step = 'STEP2';
                } else {
                    $this->step = 'STEP3';
                }
            }

        } catch (\Throwable $e) {
            $this->xlsxErrors[] = [
                'sheet'   => 'システム',
                'line'    => null,
                'message' => 'ファイルの読み込みに失敗しました：' . $e->getMessage(),
            ];
        }
    }

    /**
     * アップロードされたシートの中に画像カラムを持つものがあるか
     */
    private function hasImageSheets(): bool
    {
        foreach (array_keys($this->sheets) as $sheetName) {
            $tableName = MstTableMap::getTableName($sheetName);
            if ($tableName && MstTableMap::hasImage($tableName)) {
                return true;
            }
        }
        return false;
    }

    // ===== STEP2: 画像チャンクアップロード =====

    /**
     * フロントエンドから10枚ずつ送られてくる
     * $chunk = [['name' => 'toyota.jpg', 'base64' => '...', 'relativePath' => 'mst_manufacturer_images/toyota.jpg'], ...]
     */
    public function uploadImageChunk(array $chunk): void
    {
        $map = $this->uploadedImageMap;

        foreach ($chunk as $file) {
            $relativePath = $file['relativePath'] ?? '';
            $parts        = explode('/', $relativePath);

            $tableName = $parts[1] ?? '';
            $fileName  = implode('/', array_slice($parts, 2));

            \Log::info('before dedup', ['fileName' => $fileName]);

            // 二重拡張子を除去 例: LEXUS/ct.png.png → LEXUS/ct.png
            $fileParts = explode('.', $fileName);
            $lastName  = array_slice($fileParts, -1)[0];
            $secLast   = array_slice($fileParts, -2, 1)[0] ?? '';
            if ($lastName === $secLast) {
                $fileName = implode('.', array_slice($fileParts, 0, -1));
            }

            \Log::info('after dedup', ['fileName' => $fileName]);

            if (!$tableName || !MstTableMap::hasImage($tableName)) {
                continue;
            }

            $localPath = 'mst-image-uploads/' . $tableName . '/' . $fileName;
            Storage::disk('local')->put($localPath, base64_decode($file['base64']));

            if (!isset($map[$tableName])) {
                $map[$tableName] = [];
            }
            if (!in_array($fileName, $map[$tableName])) {
                $map[$tableName][] = $fileName;
            }

            $this->imageUploadCount++;
        }

        $this->uploadedImageMap = $map;
    }

    /**
     * 全チャンクアップロード完了後に呼ばれる
     * xlsxのfile_pathと突き合わせバリデーション
     */
    public function validateImages(): void
    {
        $this->imageErrors = [];

        foreach ($this->sheets as $sheetName => $rows) {
            $tableName = MstTableMap::getTableName($sheetName);
            if (!$tableName || !MstTableMap::hasImage($tableName)) {
                continue;
            }

            // このテーブルの is_image カラムを取得
            $imageColumns = \App\Constants\MstTableColumns::getImageColumns($tableName);
            if (empty($imageColumns)) continue;

            $uploadedFiles = $this->uploadedImageMap[$tableName] ?? [];

            foreach ($rows as $rowIndex => $row) {
                $lineNumber = $rowIndex + 2;
                foreach ($imageColumns as $column) {
                    $value = $row[$column['name']] ?? null;
                    if (empty($value)) continue;

                    // ファイル名のみ比較（パスが含まれていても basename で取得）
                    $fileName = basename($value);
                    if (!in_array($fileName, $uploadedFiles)) {
                        $this->imageErrors[] = [
                            'sheet'   => $sheetName,
                            'line'    => $lineNumber,
                            'message' => "{$column['label_ja']}「{$fileName}」がアップロードされたフォルダに存在しません",
                        ];
                    }
                }
            }
        }

        if (empty($this->imageErrors)) {
            $this->step = 'STEP3';
        }
    }

    // ===== バージョンタイプ変更 =====

    public function updateVersionType(string $type): void
    {
        $this->versionType = $type;
        $this->version     = MstVersion::generateNextVersion($type);
    }

    // ===== STEP3: データ投入 =====

    public function import(): void
    {
        if (!empty($this->xlsxErrors) || !empty($this->imageErrors)) return;

        $this->isImporting = true;

        try {
            $importer   = new MstImportService();
            $mstVersion = $importer->import(
                sheets:      $this->sheets,
                version:     $this->version,
                description: $this->description,
            );

            // S3への画像アップロード
            $this->uploadImagesToS3();

            // 一時ファイル削除
            $this->cleanupTempFiles();

            $this->importCompleted = true;
            $this->step            = 'DONE';

            Notification::make()
                ->title("バージョン {$mstVersion->version} のデータ投入が完了しました")
                ->success()
                ->send();

        } catch (\Throwable $e) {
            Notification::make()
                ->title('データ投入に失敗しました')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isImporting = false;
        }
    }

    /**
     * ローカルに一時保存した画像をS3にアップロード
     * mst_manufacturer_images/toyota.jpg → mst/manufacturers/toyota.jpg
     */
    private function uploadImagesToS3(): void
    {
        foreach ($this->uploadedImageMap as $tableName => $fileNames) {
            $s3Folder = MstTableMap::getImageFolder($tableName);
            if (!$s3Folder) continue;

            foreach ($fileNames as $fileName) {
                $localPath = 'mst-image-uploads/' . $tableName . '/' . $fileName;
                if (!Storage::disk('local')->exists($localPath)) continue;

                $s3Path   = $s3Folder . $fileName;
                $contents = Storage::disk('local')->get($localPath);

                // 同名ファイルはS3上書きで問題ないが
                // 二重拡張子の古いファイルを削除
                // 例: ct.png をアップロードする前に ct.png.png を削除
                $ext         = pathinfo($fileName, PATHINFO_EXTENSION);
                $oldFileName = $fileName . '.' . $ext; // ct.png.png
                $oldS3Path   = $s3Folder . $oldFileName;
                if (Storage::disk('s3')->exists($oldS3Path)) {
                    Storage::disk('s3')->delete($oldS3Path);
                }

                Storage::disk('s3')->put($s3Path, $contents);
            }
        }
    }

    /**
     * 一時ファイルを削除
     */
    private function cleanupTempFiles(): void
    {
        // xlsxファイル削除
        if ($this->uploadedFilePath) {
            Storage::disk('local')->delete($this->uploadedFilePath);
            $this->uploadedFilePath = null;
        }

        // 画像一時ファイル削除
        if (!empty($this->uploadedImageMap)) {
            Storage::disk('local')->deleteDirectory('mst-image-uploads');
            $this->uploadedImageMap = [];
        }
    }

    // ===== STEP戻る =====

    public function backToStep1(): void
    {
        $this->step           = 'STEP1';
        $this->xlsxErrors     = [];
        $this->previewCounts  = [];
        $this->sheets         = [];
        $this->uploadedImageMap = [];
        $this->imageErrors    = [];
        $this->imageUploadCount = 0;

        if ($this->uploadedFilePath) {
            Storage::disk('local')->delete($this->uploadedFilePath);
            $this->uploadedFilePath = null;
        }
    }

    public function backToStep2(): void
    {
        $this->step        = 'STEP2';
        $this->imageErrors = [];
        $this->uploadedImageMap = [];
        $this->imageUploadCount = 0;
        Storage::disk('local')->deleteDirectory('mst-image-uploads');
    }

    // ===== スプレッドシート読み込み =====

    private function readSpreadsheet(string $filePath): array
    {
        $spreadsheet = IOFactory::load(storage_path('app/private/' . $filePath));
        $sheets      = [];

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $sheet  = $spreadsheet->getSheetByName($sheetName);
            $rows   = $sheet->toArray(null, true, true, false);
            $header = array_shift($rows);

            $sheets[$sheetName] = array_map(
                fn ($row) => array_combine($header, $row),
                array_filter($rows, fn ($row) => !empty(array_filter($row)))
            );
        }

        return $sheets;
    }

    // ===== 表示用ヘルパー =====

    public function getTotalImageCount(): int
    {
        return array_sum(array_map('count', $this->uploadedImageMap));
    }
}