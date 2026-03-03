<?php

namespace App\Filament\Resources\MstRegionsResource\Pages;

use App\Filament\Resources\MstRegionsResource;
use Filament\Resources\Pages\ListRecords;

class ListMstRegions extends ListRecords
{
    protected static string $resource = MstRegionsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
