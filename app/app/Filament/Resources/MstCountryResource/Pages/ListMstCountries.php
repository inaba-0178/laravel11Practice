<?php

namespace App\Filament\Resources\MstCountryResource\Pages;

use App\Filament\Resources\MstCountriesResource;
use Filament\Resources\Pages\ListRecords;

class ListMstCountries extends ListRecords
{
    protected static string $resource = MstCountriesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
