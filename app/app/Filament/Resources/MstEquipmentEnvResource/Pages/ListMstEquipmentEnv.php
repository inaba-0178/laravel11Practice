<?php

namespace App\Filament\Resources\MstEquipmentEnvResource\Pages;

use App\Filament\Resources\MstEquipmentEnvResource;
use Filament\Resources\Pages\ListRecords;

class ListMstEquipmentEnv extends ListRecords
{
    protected static string $resource = MstEquipmentEnvResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
