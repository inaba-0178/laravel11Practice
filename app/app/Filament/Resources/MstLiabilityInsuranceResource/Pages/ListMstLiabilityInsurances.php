<?php

declare(strict_types=1);

namespace App\Filament\Resources\MstLiabilityInsuranceResource\Pages;

use App\Filament\Resources\MstLiabilityInsuranceResource;
use Filament\Resources\Pages\ListRecords;

class ListMstLiabilityInsurances extends ListRecords
{
    protected static string $resource = MstLiabilityInsuranceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}