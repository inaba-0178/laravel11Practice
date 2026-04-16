<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerStaffResource\Pages;

use App\Filament\Resources\DealerStaffResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDealerStaffs extends ListRecords
{
    protected static string $resource = DealerStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('スタッフを追加'),
        ];
    }
}