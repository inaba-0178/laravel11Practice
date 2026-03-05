<?php

namespace App\Filament\Resources\MstBodyTypesResource\Pages;

use App\Filament\Resources\MstBodyTypesResource;
use Filament\Resources\Pages\ListRecords;

class ListMstBodyTypes extends ListRecords
{
    protected static string $resource = MstBodyTypesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
