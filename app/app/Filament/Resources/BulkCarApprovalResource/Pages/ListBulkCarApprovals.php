<?php

declare(strict_types=1);

namespace App\Filament\Resources\BulkCarApprovalResource\Pages;

use App\Filament\Resources\BulkCarApprovalResource;
use App\Infrastructure\Eloquent\User\StkBulkUploadBatch;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Constants\CarStatus;

class ListBulkCarApprovals extends ListRecords
{
    protected static string $resource = BulkCarApprovalResource::class;

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('承認待ちあり')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('pending_count', '>', 0))
                ->badge(StkBulkUploadBatch::where('pending_count', '>', 0)->count())
                ->badgeColor('warning'),

            'rejected' => Tab::make('差し戻しあり')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rejected_count', '>', 0)->where('pending_count', 0))
                ->badge(StkBulkUploadBatch::where('rejected_count', '>', 0)->where('pending_count', 0)->count())
                ->badgeColor('danger'),

            'approved' => Tab::make('承認済み公開前')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereHas('cars', fn ($q) => $q->where('status', CarStatus::APPROVED_PENDING))
                    ->whereDoesntHave('cars', fn ($q) => $q->where('status', CarStatus::PENDING))
                ),

            'published' => Tab::make('公開中')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('cars', fn ($q) => $q->where('status', 'available'))),

            'all' => Tab::make('すべて'),
        ];
    }
}