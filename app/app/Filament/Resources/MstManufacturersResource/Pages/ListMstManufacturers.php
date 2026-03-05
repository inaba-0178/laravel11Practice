<?php

namespace App\Filament\Resources\MstManufacturersResource\Pages;

use App\Filament\Resources\MstManufacturersResource;
use Filament\Resources\Pages\ListRecords;

class ListMstManufacturers extends ListRecords
{
    protected static string $resource = MstManufacturersResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
