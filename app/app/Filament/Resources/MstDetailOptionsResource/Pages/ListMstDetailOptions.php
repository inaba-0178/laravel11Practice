<?php

namespace App\Filament\Resources\MstDetailOptionsResource\Pages;

use App\Filament\Resources\MstDetailOptionsResource;
use Filament\Resources\Pages\ListRecords;

class ListMstDetailOptions extends ListRecords
{
    protected static string $resource = MstDetailOptionsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
