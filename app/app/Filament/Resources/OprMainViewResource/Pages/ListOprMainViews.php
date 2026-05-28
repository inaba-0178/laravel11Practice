<?php

declare(strict_types=1);

namespace App\Filament\Resources\OprMainViewResource\Pages;

use App\Filament\Resources\OprMainViewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOprMainViews extends ListRecords
{
    protected static string $resource = OprMainViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('メインビューを追加'),
        ];
    }
}