<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoanSettingResource\Pages;

use App\Filament\Resources\LoanSettingResource;
use Filament\Resources\Pages\ListRecords;

class ListLoanSettings extends ListRecords
{
    protected static string $resource = LoanSettingResource::class;
}