<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerShopResource\Pages;

use App\Filament\Resources\DealerShopResource;
use App\Filament\Pages\DealerShopDetail;
use Filament\Resources\Pages\EditRecord;

class EditDealerShop extends EditRecord
{
    protected static string $resource   = DealerShopResource::class;
    protected static string $view       = 'filament.pages.dealer-shop-edit';

    protected function getRedirectUrl(): string
    {
        return DealerShopDetail::getUrl(['id' => $this->record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}