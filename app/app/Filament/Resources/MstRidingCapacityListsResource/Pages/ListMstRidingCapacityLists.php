<?php

namespace App\Filament\Resources\MstRidingCapacityListsResource\Pages;

use App\Filament\Resources\MstRidingCapacityListsResource;
use Filament\Resources\Pages\ListRecords;

class ListMstRidingCapacityLists extends ListRecords
{
    protected static string $resource = MstRidingCapacityListsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
