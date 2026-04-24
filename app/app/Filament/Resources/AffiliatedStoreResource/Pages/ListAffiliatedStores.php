<?php

declare(strict_types=1);

namespace App\Filament\Resources\AffiliatedStoreResource\Pages;

use App\Filament\Resources\AffiliatedStoreResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListAffiliatedStores extends ListRecords
{
    protected static string $resource = AffiliatedStoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('系列店・提携店を申請する')
                ->url(AffiliatedStoreResource::getUrl('create')),
        ];
    }
}