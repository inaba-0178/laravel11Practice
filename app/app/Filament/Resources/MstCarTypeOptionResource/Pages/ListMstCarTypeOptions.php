<?php

namespace App\Filament\Resources\MstCarTypeOptionResource\Pages;

use App\Filament\Resources\MstCarTypeOptionResource;
use Filament\Resources\Pages\ListRecords;

class ListMstCarTypeOptions extends ListRecords
{
    protected static string $resource = MstCarTypeOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
