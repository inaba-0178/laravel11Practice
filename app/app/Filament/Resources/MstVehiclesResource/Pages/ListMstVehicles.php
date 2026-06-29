<?php

namespace App\Filament\Resources\MstVehiclesResource\Pages;

use App\Filament\Resources\MstVehiclesResource;
use Filament\Resources\Pages\ListRecords;

class ListMstVehicles extends ListRecords
{
    protected static string $resource = MstVehiclesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
