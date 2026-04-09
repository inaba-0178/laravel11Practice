<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerFeeResource\Pages;

use App\Filament\Resources\DealerFeeResource;
use App\Filament\Resources\DealerFeeResource\Concerns\ValidatesFeeData;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Pages\DealerFeeDetail;
use App\Infrastructure\Eloquent\User\StkDealerFee;

class EditDealerFee extends EditRecord
{
    use ValidatesFeeData;

    protected static string $resource = DealerFeeResource::class;

    protected function getRedirectUrl(): string
    {
        return DealerFeeDetail::getUrl(['id' => $this->record->id]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->validateFeeData($data);

        // is_defaultがtrueの場合は他のプランをfalseにする
        if (!empty($data['is_default'])) {
            StkDealerFee::where('dealer_id', Auth::user()->dealer_id)
                ->where('id', '!=', $this->record->id)
                ->update(['is_default' => false]);
        }

        return $data;
    }
}