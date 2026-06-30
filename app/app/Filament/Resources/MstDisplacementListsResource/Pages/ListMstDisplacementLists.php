<?php

namespace App\Filament\Resources\MstDisplacementListsResource\Pages;

use App\Filament\Resources\MstDisplacementListsResource;
use Filament\Resources\Pages\ListRecords;

class ListMstDisplacementLists extends ListRecords
{
    protected static string $resource = MstDisplacementListsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
