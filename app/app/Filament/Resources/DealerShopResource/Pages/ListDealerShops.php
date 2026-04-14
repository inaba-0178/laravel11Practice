<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerShopResource\Pages;

use App\Filament\Resources\DealerShopResource;
use App\Filament\Pages\DealerShopDetail;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListDealerShops extends ListRecords
{
    protected static string $resource = DealerShopResource::class;

    public function mount(): void
    {
        $dealerId = Auth::user()?->dealer_id;
        redirect(DealerShopDetail::getUrl(['id' => $dealerId]));
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}