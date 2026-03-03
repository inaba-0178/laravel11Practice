<?php

namespace App\Filament\Resources\MstPriceListsResource\Pages;

use App\Filament\Resources\MstPriceListsResource;
use Filament\Resources\Pages\ListRecords;

class ListMstPriceLists extends ListRecords
{
    protected static string $resource = MstPriceListsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
