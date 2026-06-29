<?php

namespace App\Filament\Resources\MstSeatOptionResource\Pages;

use App\Filament\Resources\MstSeatOptionResource;
use Filament\Resources\Pages\ListRecords;

class ListMstSeatOptions extends ListRecords
{
    protected static string $resource = MstSeatOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
