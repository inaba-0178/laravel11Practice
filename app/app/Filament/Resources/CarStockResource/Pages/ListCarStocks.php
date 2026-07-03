<?php

declare(strict_types=1);

namespace App\Filament\Resources\CarStockResource\Pages;

use App\Constants\CarStatus;
use App\Filament\Resources\CarStockResource;
use App\Infrastructure\Eloquent\User\StkCar;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListCarStocks extends ListRecords
{
    protected static string $resource = CarStockResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        $dealerId = Auth::user()?->getEffectiveDealerId();

        return [
            'available' => Tab::make('公開中')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CarStatus::AVAILABLE))
                ->badge(StkCar::where('dealer_id', $dealerId)->where('status', CarStatus::AVAILABLE)->count())
                ->badgeColor('success'),

            'approved_pending' => Tab::make('承認済み公開前')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CarStatus::APPROVED_PENDING))
                ->badge(StkCar::where('dealer_id', $dealerId)->where('status', CarStatus::APPROVED_PENDING)->count())
                ->badgeColor('info'),

            'scheduled' => Tab::make('公開日時指定')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CarStatus::SCHEDULED))
                ->badge(StkCar::where('dealer_id', $dealerId)->where('status', CarStatus::SCHEDULED)->count())
                ->badgeColor('info'),

            'reserved' => Tab::make('予約中')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CarStatus::RESERVED))
                ->badge(StkCar::where('dealer_id', $dealerId)->where('status', CarStatus::RESERVED)->count())
                ->badgeColor('info'),

            'sold' => Tab::make('販売終了')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CarStatus::SOLD)),

            'publish_ended' => Tab::make('公開終了')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CarStatus::PUBLISH_ENDED)),
        ];
    }
}