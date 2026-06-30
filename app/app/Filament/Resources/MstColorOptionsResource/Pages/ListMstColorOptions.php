<?php

namespace App\Filament\Resources\MstColorOptionsResource\Pages;

use App\Filament\Resources\MstColorOptionsResource;
use Filament\Resources\Pages\ListRecords;

class ListMstColorOptions extends ListRecords
{
    protected static string $resource = MstColorOptionsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
