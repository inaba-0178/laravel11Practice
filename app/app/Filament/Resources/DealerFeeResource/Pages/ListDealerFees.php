<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerFeeResource\Pages;

use App\Filament\Resources\DealerFeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDealerFees extends ListRecords
{
    protected static string $resource = DealerFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('諸費用プランを追加'),
        ];
    }
}