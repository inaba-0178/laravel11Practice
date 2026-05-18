<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Constants\Role\RoleManagement;
use App\Domain\Mst\Services\MstImportService;
use App\Domain\Mst\Services\MstValidatorService;
use App\Infrastructure\Eloquent\Mst\MstVersion;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

class MstUpload extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = NavigationGroup::MST_UPDATE_GROUP->value;
    protected static ?int    $navigationSort  = NavigationSort::MST_UPLOAD->value;
    protected static ?string $title           = 'マスタアップロード';
    protected static string  $view            = 'filament.pages.mst-upload';

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, RoleManagement::MST_OPERATOR_ROLES);
    }

    // ===== プロパティ =====
    public ?string $uploadedFilePath = null;
    public array   $errors           = [];
    public array   $previewCounts    = [];
    public array   $sheets           = [];
    public string  $version          = '';
    public string  $description      = '';
    public string  $versionType      = 'patch';
    public bool    $isValidating     = false;
    public bool    $isImporting      = false;
    public int     $importProgress   = 0;
    public int     $importTotal      = 0;
    public string  $currentTable     = '';
    public bool    $showModal        = false;
    public bool    $importCompleted  = false;

    public function mount(): void
    {
        $this->version = MstVersion::generateNextVersion('patch');
    }

    // ===== ファイルアップロード・バリデーション =====
    public function uploadAndValidate(): void
    {
        $this->validate([
            'uploadedFilePath' => 'required',
        ], [
            'uploadedFilePath.required' => 'ファイルを選択してください',
        ]);

        $this->isValidating  = true;
        $this->errors        = [];
        $this->previewCounts = [];
        $this->showModal     = true;

        try {
            // スプレッドシート読み込み
            $this->sheets = $this->readSpreadsheet($this->uploadedFilePath);

            // バリデーション
            $validator    = new MstValidatorService();
            $this->errors = $validator->validate($this->sheets);

            if (empty($this->errors)) {
                // プレビュー件数取得
                $importer            = new MstImportService();
                $this->previewCounts = $importer->getPreviewCounts($this->sheets);
                $this->version       = MstVersion::generateNextVersion($this->versionType);
            }

        } catch (\Throwable $e) {
            $this->errors[] = [
                'sheet'   => 'システム',
                'line'    => null,
                'message' => 'ファイルの読み込みに失敗しました：' . $e->getMessage(),
            ];
        } finally {
            $this->isValidating = false;
        }
    }

    // ===== データ投入 =====
    public function import(): void
    {
        if (!empty($this->errors)) return;

        $this->isImporting   = true;
        $this->importTotal   = count($this->sheets);
        $this->importProgress = 0;

        try {
            $importer   = new MstImportService();
            $mstVersion = $importer->import(
                sheets:      $this->sheets,
                version:     $this->version,
                description: $this->description,
                onProgress:  function (string $tableName, int $current) {
                    $this->currentTable    = $tableName;
                    $this->importProgress  = $current;
                }
            );

            $this->importCompleted = true;
            $this->showModal       = false;

            // 一時ファイル削除
            if ($this->uploadedFilePath) {
                Storage::disk('local')->delete($this->uploadedFilePath);
                $this->uploadedFilePath = null;
            }

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

    // ===== バージョンタイプ変更 =====
    public function updateVersionType(string $type): void
    {
        $this->versionType = $type;
        $this->version     = MstVersion::generateNextVersion($type);
    }

    // ===== スプレッドシート読み込み =====
    private function readSpreadsheet(string $filePath): array
    {
        $spreadsheet = IOFactory::load(storage_path('app/private/' . $filePath));
        $sheets      = [];

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $sheet  = $spreadsheet->getSheetByName($sheetName);
            $rows   = $sheet->toArray(null, true, true, false);
            $header = array_shift($rows); // 1行目をヘッダーとして取得

            $sheets[$sheetName] = array_map(
                fn ($row) => array_combine($header, $row),
                array_filter($rows, fn ($row) => !empty(array_filter($row)))
            );
        }

        return $sheets;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }
}