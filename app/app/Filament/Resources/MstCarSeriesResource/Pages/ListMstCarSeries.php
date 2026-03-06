<?php

namespace App\Filament\Resources\MstCarSeriesResource\Pages;

use App\Filament\Resources\MstCarSeriesResource;
use Filament\Resources\Pages\ListRecords;

class ListMstCarSeries extends ListRecords
{
    protected static string $resource = MstCarSeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
