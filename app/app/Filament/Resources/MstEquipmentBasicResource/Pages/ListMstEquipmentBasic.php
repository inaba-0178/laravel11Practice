<?php

namespace App\Filament\Resources\MstEquipmentBasicResource\Pages;

use App\Filament\Resources\MstEquipmentBasicResource;
use Filament\Resources\Pages\ListRecords;

class ListMstEquipmentBasic extends ListRecords
{
    protected static string $resource = MstEquipmentBasicResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
