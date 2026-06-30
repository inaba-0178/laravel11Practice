<?php

namespace App\Filament\Resources\MstFeaturedBodyTypesResource\Pages;

use App\Filament\Resources\MstFeaturedBodyTypesResource;
use Filament\Resources\Pages\ListRecords;

class ListMstFeaturedBodyTypes extends ListRecords
{
    protected static string $resource = MstFeaturedBodyTypesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
