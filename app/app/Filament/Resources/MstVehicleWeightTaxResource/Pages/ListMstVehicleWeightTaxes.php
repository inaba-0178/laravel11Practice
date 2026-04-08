<?php

declare(strict_types=1);

namespace App\Filament\Resources\MstVehicleWeightTaxResource\Pages;

use App\Filament\Resources\MstVehicleWeightTaxResource;
use Filament\Resources\Pages\ListRecords;

class ListMstVehicleWeightTaxes extends ListRecords
{
    protected static string $resource = MstVehicleWeightTaxResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}