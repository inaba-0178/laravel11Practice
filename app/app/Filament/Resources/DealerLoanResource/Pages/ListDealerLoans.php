<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerLoanResource\Pages;

use App\Filament\Resources\DealerLoanResource;
use Filament\Resources\Pages\ListRecords;

class ListDealerLoans extends ListRecords
{
    protected static string $resource = DealerLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()->label('ローンプランを追加'),
        ];
    }
}