<?php

namespace App\Filament\Resources\MstLoanPlanResource\Pages;

use App\Filament\Resources\MstLoanPlanResource;
use Filament\Resources\Pages\ListRecords;

class ListMstLoanPlans extends ListRecords
{
    protected static string $resource = MstLoanPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
