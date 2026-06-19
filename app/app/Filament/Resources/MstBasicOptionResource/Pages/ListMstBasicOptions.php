<?php

namespace App\Filament\Resources\MstBasicOptionResource\Pages;

use App\Filament\Resources\MstBasicOptionsResource;
use Filament\Resources\Pages\ListRecords;

class ListMstBasicOptions extends ListRecords
{
    protected static string $resource = MstBasicOptionsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
