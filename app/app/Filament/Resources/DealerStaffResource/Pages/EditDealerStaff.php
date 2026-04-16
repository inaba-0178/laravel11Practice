<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerStaffResource\Pages;

use App\Filament\Resources\DealerStaffResource;
use App\Filament\Pages\DealerStaffDetail;
use Filament\Resources\Pages\EditRecord;

class EditDealerStaff extends EditRecord
{
    protected static string $resource   = DealerStaffResource::class;
    protected static string $view       = 'filament.pages.dealer-staff-edit';

    protected function getRedirectUrl(): string
    {
        return DealerStaffDetail::getUrl(['id' => $this->record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}