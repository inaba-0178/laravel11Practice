<?php

declare(strict_types=1);

namespace App\Filament\Resources\CarApprovalResource\Pages;

use App\Constants\CarStatus;
use App\Filament\Resources\CarApprovalResource;
use App\Infrastructure\Eloquent\User\StkCar;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCarApprovals extends ListRecords
{
    protected static string $resource = CarApprovalResource::class;

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('承認待ち')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CarStatus::PENDING))
                ->badge(StkCar::where('status', CarStatus::PENDING)->count())
                ->badgeColor('warning'),

            'rejected' => Tab::make('差し戻し済み')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CarStatus::REJECTED))
                ->badge(StkCar::where('status', CarStatus::REJECTED)->count())
                ->badgeColor('danger'),

            'available' => Tab::make('承認済み')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CarStatus::AVAILABLE)),

            'all' => Tab::make('すべて')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [
                    CarStatus::PENDING,
                    CarStatus::REJECTED,
                    CarStatus::AVAILABLE,
                    CarStatus::DRAFT,
                ])),
        ];
    }
}