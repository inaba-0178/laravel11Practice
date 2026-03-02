<?php

namespace App\Filament\Resources\MstAreasResource\Pages;

use App\Filament\Resources\MstAreasResource;
use Filament\Resources\Pages\ListRecords;

class ListMstAreas extends ListRecords
{
    protected static string $resource = MstAreasResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
