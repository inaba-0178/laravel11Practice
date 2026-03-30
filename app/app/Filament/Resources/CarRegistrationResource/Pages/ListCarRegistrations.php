<?php

declare(strict_types=1);

namespace App\Filament\Resources\CarRegistrationResource\Pages;

use App\Filament\Resources\CarRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarRegistrations extends ListRecords
{
    protected static string $resource = CarRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('車両を登録する'),
        ];
    }
}