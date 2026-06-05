<?php

declare(strict_types=1);

namespace App\Filament\Resources\OprSettingResource\Pages;

use App\Filament\Resources\OprSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOprSettings extends ListRecords
{
    protected static string $resource = OprSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('設定を追加'),
        ];
    }
}