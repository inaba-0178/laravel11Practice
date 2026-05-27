<?php

declare(strict_types=1);

namespace App\Domain\Mst\Services;

use App\Constants\MstTableColumns;
use App\Constants\MstTableMap;
use Illuminate\Support\Facades\Storage;
use App\Domain\Mst\DataTransformers\MstRowTransformer;

class MstValidatorService
{
    private array $errors = [];

    /**
     * 通常バリデーション（S3画像チェックあり）
     * ※ 既存の呼び出し元との後方互換のため残す
     */
    public function validate(array $sheets): array
    {
        return $this->runValidation($sheets, checkImages: true);
    }

    /**
     * 画像チェックなしバリデーション（STEP1で使用）
     * 画像はSTEP2で別途チェックするためここではスキップ
     */
    public function validateWithoutImages(array $sheets): array
    {
        return $this->runValidation($sheets, checkImages: false);
    }

    private function runValidation(array $sheets, bool $checkImages): array
    {
        $this->errors = [];

        $this->validateSheetNames($sheets);

        if (!empty($this->errors)) {
            return $this->errors;
        }

        foreach ($sheets as $sheetName => $rows) {
            $tableName      = MstTableMap::getTableName($sheetName);
            $mappedRows     = MstRowTransformer::mapHeaders($rows, $tableName);
            $normalizedRows = MstRowTransformer::normalizeNumbers($mappedRows);
            // $resolvedRows   = MstRowTransformer::resolveVehicleIds($normalizedRows, $tableName); ← 削除
            $columns        = MstTableColumns::getColumns($tableName);

            foreach ($normalizedRows as $rowIndex => $row) { 
                $lineNumber = $rowIndex + 2;

                foreach ($columns as $column) {
                    $value = $row[$column['name']] ?? null;

                    if ($column['required'] && ($value === null || $value === '')) {
                        $this->addError($sheetName, $lineNumber, "{$column['label_ja']}は必須です");
                        continue;
                    }

                    if ($value === null || $value === '') continue;

                    match($column['type']) {
                        'integer' => $this->validateInteger($sheetName, $lineNumber, $column['label_ja'], $value),
                        'decimal' => $this->validateDecimal($sheetName, $lineNumber, $column['label_ja'], $value),
                        'boolean' => $this->validateBoolean($sheetName, $lineNumber, $column['label_ja'], $value),
                        'json'    => $this->validateJson($sheetName, $lineNumber, $column['label_ja'], $value),
                        'enum'    => $this->validateEnum($sheetName, $lineNumber, $column['label_ja'], $value, $column['values'] ?? []),
                        default   => null,
                    };

                    // 画像チェックは $checkImages フラグで制御
                    if ($checkImages && ($column['is_image'] ?? false)) {
                        $this->validateImageExists($sheetName, $lineNumber, $column['label_ja'], $value, $tableName);
                    }
                }
            }
        }

        return $this->errors;
    }

    private function validateInteger(string $sheet, int $line, string $column, $value): void
    {
        if (!is_numeric($value) || (int)$value != $value) {
            $this->addError($sheet, $line, "{$column}は整数でなければなりません（値：{$value}）");
        }
    }

    private function validateDecimal(string $sheet, int $line, string $column, $value): void
    {
        if (!is_numeric($value)) {
            $this->addError($sheet, $line, "{$column}は数値でなければなりません（値：{$value}）");
        }
    }

    private function validateJson(string $sheet, int $line, string $column, $value): void
    {
        json_decode($value);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->addError($sheet, $line, "{$column}は有効なJSON形式でなければなりません（値：{$value}）");
        }
    }

    private function validateEnum(string $sheet, int $line, string $column, $value, array $values): void
    {
        if (!in_array($value, $values)) {
            $validValues = implode('/', $values);
            $this->addError($sheet, $line, "{$column}は{$validValues}のいずれかでなければなりません（値：{$value}）");
        }
    }

    private function validateImageExists(string $sheet, int $line, string $column, string $value, string $tableName): void
    {
        // 開発環境ではスキップ
        if (config('app.env') === 'local') return;

        $folder = MstTableMap::getImageFolder($tableName);
        if (!$folder) return;

        $path = $folder . $value;
        if (!Storage::disk('s3')->exists($path)) {
            $this->addError($sheet, $line, "{$column}のファイルがS3に存在しません（パス：{$path}）");
        }
    }

    private function addError(string $sheet, ?int $line, string $message): void
    {
        $this->errors[] = [
            'sheet'   => $sheet,
            'line'    => $line,
            'message' => $message,
        ];
    }

    private function validateSheetNames(array $sheets): void
    {
        $validSheetNames = array_keys(MstTableMap::TABLES);

        foreach (array_keys($sheets) as $sheetName) {
            if (!in_array($sheetName, $validSheetNames)) {
                $this->addError($sheetName, null, "シート名「{$sheetName}」は無効です");
            }
        }

        foreach ($validSheetNames as $validName) {
            if (!array_key_exists($validName, $sheets)) {
                $this->addError($validName, null, "シート「{$validName}」が存在しません");
            }
        }
    }

    private function validateBoolean(string $sheet, int $line, string $column, $value): void
    {
        $valid = [0, 1, '0', '1', true, false, 'TRUE', 'FALSE', 'true', 'false'];
        if (!in_array($value, $valid, true)) {
            $this->addError($sheet, $line, "{$column}は0または1でなければなりません（値：{$value}）");
        }
    }
}