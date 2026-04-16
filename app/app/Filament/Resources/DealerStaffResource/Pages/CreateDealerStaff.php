<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerStaffResource\Pages;

use App\Filament\Resources\DealerStaffResource;
use App\Filament\Pages\DealerStaffDetail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDealerStaff extends CreateRecord
{
    protected static string $resource = DealerStaffResource::class;

    protected function getRedirectUrl(): string
    {
        return DealerStaffResource::getUrl('edit', ['record' => $this->record->id]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dealer_id'] = Auth::user()->dealer_id;
        return $data;
    }
}