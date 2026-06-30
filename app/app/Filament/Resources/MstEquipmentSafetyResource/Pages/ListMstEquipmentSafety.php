<?php

namespace App\Filament\Resources\MstEquipmentSafetyResource\Pages;

use App\Filament\Resources\MstEquipmentSafetyResource;
use Filament\Resources\Pages\ListRecords;

class ListMstEquipmentSafety extends ListRecords
{
    protected static string $resource = MstEquipmentSafetyResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
