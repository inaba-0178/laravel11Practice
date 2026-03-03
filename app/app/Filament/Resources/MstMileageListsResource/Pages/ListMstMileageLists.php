<?php

namespace App\Filament\Resources\MstMileageListsResource\Pages;

use App\Filament\Resources\MstMileageListsResource;
use Filament\Resources\Pages\ListRecords;

class ListMstMileageLists extends ListRecords
{
    protected static string $resource = MstMileageListsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
