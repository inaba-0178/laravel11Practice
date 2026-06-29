<?php

namespace App\Filament\Resources\MstVehicleTaxResource\Pages;

use App\Filament\Resources\MstVehicleTaxResource;
use Filament\Resources\Pages\ListRecords;

class ListMstVehicleTaxes extends ListRecords
{
    protected static string $resource = MstVehicleTaxResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
