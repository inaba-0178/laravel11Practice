<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerFeeResource\Pages;

use App\Filament\Resources\DealerFeeResource;
use App\Filament\Resources\DealerFeeResource\Concerns\ValidatesFeeData;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Infrastructure\Eloquent\User\StkDealerFee;

class CreateDealerFee extends CreateRecord
{
    use ValidatesFeeData;

    protected static string $resource = DealerFeeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->validateFeeData($data);

        // is_defaultがtrueの場合は他のプランをfalseにする
        if (!empty($data['is_default'])) {
            StkDealerFee::where('dealer_id', Auth::user()->dealer_id)
                ->update(['is_default' => false]);
        }

        $data['dealer_id'] = Auth::user()->dealer_id;
        return $data;
    }
}