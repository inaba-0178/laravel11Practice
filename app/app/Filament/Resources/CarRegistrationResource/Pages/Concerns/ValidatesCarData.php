<?php

declare(strict_types=1);

namespace App\Filament\Resources\CarRegistrationResource\Concerns;

use Filament\Notifications\Notification;

trait ValidatesCarData
{
    private function validateCarData(array $data): void
    {
        // ===== 必須・選択系 =====
        $requiredSelects = [
            'manufacturer_id'    => 'メーカー',
            'series_id'          => '車体名',
            'vehicle_id'         => '年式・グレード',
            'model_year'         => '年式',
            'price_display_type' => '価格表示方法',
            'repair_history'     => '修復歴',
            'region_id'          => '地域（都道府県）',
            'inspection_status'  => '車検状態',
            'steering_wheel'     => 'ハンドル',
            'slide_door'         => 'スライドドア',
        ];

        foreach ($requiredSelects as $field => $label) {
            if (empty($data[$field])) {
                $this->sendValidationError("{$label}を選択してください。");
                return;
            }
        }

        // ===== 文字列・空チェック =====
        if (empty(trim($data['color'] ?? ''))) {
            $this->sendValidationError('ボディカラーを入力してください。');
            return;
        }

        // ===== 数値・0以上の整数チェック =====
        $intFields = [
            'price'   => ['label' => '支払価格', 'required' => true],
            'mileage' => ['label' => '走行距離', 'required' => true],
        ];

        foreach ($intFields as $field => $config) {
            $value = $data[$field] ?? null;
            if ($config['required'] && $value === null) {
                $this->sendValidationError("{$config['label']}を入力してください。");
                return;
            }
            if ($value !== null && (!is_numeric($value) || (int)$value < 0 || (string)(int)$value !== (string)$value)) {
                $this->sendValidationError("{$config['label']}は0以上の整数で入力してください。");
                return;
            }
        }

        // ===== 任意・数値チェック =====
        $optionalIntFields = [
            'displacement'   => '排気量',
            'number_of_doors' => 'ドア数',
            'riding_capacity' => '乗車定員',
            'recycle_fee'    => 'リサイクル預託金',
        ];

        foreach ($optionalIntFields as $field => $label) {
            $value = $data[$field] ?? null;
            if ($value !== null && $value !== '' && (!is_numeric($value) || (int)$value < 0 || (string)(int)$value !== (string)$value)) {
                $this->sendValidationError("{$label}は0以上の整数で入力してください。");
                return;
            }
        }

        // ===== 年式の範囲チェック =====
        $modelYear = $data['model_year'] ?? null;
        if ($modelYear !== null && ($modelYear < 1900 || $modelYear > now()->year)) {
            $this->sendValidationError('年式は1900年〜' . now()->year . '年の範囲で入力してください。');
            return;
        }
    }

    private function sendValidationError(string $message): void
    {
        Notification::make()
            ->title('不正な値が含まれています')
            ->body($message)
            ->danger()
            ->send();

        $this->halt();
    }
}