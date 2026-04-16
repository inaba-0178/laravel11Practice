<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerContentResource\Pages;

use App\Filament\Resources\DealerContentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDealerContent extends CreateRecord
{
    protected static string $resource = DealerContentResource::class;

    protected function getRedirectUrl(): string
    {
        return DealerContentResource::getUrl('edit', ['record' => $this->record->id]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dealer_id'] = Auth::user()->dealer_id;
        return $data;
    }
}