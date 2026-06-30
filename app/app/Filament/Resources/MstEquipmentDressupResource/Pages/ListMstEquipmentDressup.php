<?php

namespace App\Filament\Resources\MstEquipmentDressupResource\Pages;

use App\Filament\Resources\MstEquipmentDressupResource;
use Filament\Resources\Pages\ListRecords;

class ListMstEquipmentDressup extends ListRecords
{
    protected static string $resource = MstEquipmentDressupResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
