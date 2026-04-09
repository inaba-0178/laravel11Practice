<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerFeeResource\Concerns;

use Filament\Notifications\Notification;

trait ValidatesFeeData
{
    private function validateFeeData(array $data): void
    {
        $feeFields = ['registration_fee', 'garage_cert_fee', 'delivery_fee', 'maintenance_fee'];

        foreach ($feeFields as $field) {
            $value = $data[$field] ?? null;
            if (!is_numeric($value) || (int) $value < 0 || (string)(int)$value !== (string)$value) {
                Notification::make()
                    ->title('不正な値が含まれています')
                    ->body('金額は0以上の整数で入力してください。')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        if (empty(trim($data['name'] ?? ''))) {
            Notification::make()
                ->title('不正な値が含まれています')
                ->body('プラン名を入力してください。')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}