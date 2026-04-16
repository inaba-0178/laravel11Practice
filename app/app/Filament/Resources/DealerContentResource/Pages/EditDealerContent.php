<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerContentResource\Pages;

use App\Filament\Resources\DealerContentResource;
use App\Filament\Pages\DealerContentDetail;
use Filament\Resources\Pages\EditRecord;

class EditDealerContent extends EditRecord
{
    protected static string $resource = DealerContentResource::class;
    protected static string $view     = 'filament.pages.dealer-content-edit';

    protected function getRedirectUrl(): string
    {
        return DealerContentDetail::getUrl(['id' => $this->record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}