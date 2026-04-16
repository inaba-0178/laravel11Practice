<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerContentResource\Pages;

use App\Filament\Resources\DealerContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDealerContents extends ListRecords
{
    protected static string $resource = DealerContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('コンテンツを追加'),
        ];
    }
}