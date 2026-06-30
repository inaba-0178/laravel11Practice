<?php

namespace App\Filament\Resources\MstFeaturedBrandsResource\Pages;

use App\Filament\Resources\MstFeaturedBrandsResource;
use Filament\Resources\Pages\ListRecords;

class ListMstFeaturedBrands extends ListRecords
{
    protected static string $resource = MstFeaturedBrandsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
