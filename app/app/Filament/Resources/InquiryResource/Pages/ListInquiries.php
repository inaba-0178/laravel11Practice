<?php

declare(strict_types=1);

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Constants\InquiryStatus;
use App\Filament\Resources\InquiryResource;
use App\Infrastructure\Eloquent\User\StkInquiry;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        $baseQuery = fn (Builder $query) => $query->when(
            !in_array(Auth::user()?->role, ['super', 'admin']) && Auth::user()?->dealer_id,
            fn ($q) => $q->where('dealer_id', Auth::user()->dealer_id)
        );

        return [
            'new' => Tab::make('新規')
                ->modifyQueryUsing(fn (Builder $query) => $baseQuery($query)
                    ->where('status', InquiryStatus::NEW))
                ->badge(StkInquiry::where('status', InquiryStatus::NEW)
                    ->when(
                        !in_array(Auth::user()?->role, ['super', 'admin']) && Auth::user()?->dealer_id,
                        fn ($q) => $q->where('dealer_id', Auth::user()->dealer_id)
                    )->count())
                ->badgeColor('warning'),

            'draft' => Tab::make('一時保存')
                ->modifyQueryUsing(fn (Builder $query) => $baseQuery($query)
                    ->where('status', InquiryStatus::DRAFT))
                ->badge(StkInquiry::where('status', InquiryStatus::DRAFT)
                    ->when(
                        !in_array(Auth::user()?->role, ['super', 'admin']) && Auth::user()?->dealer_id,
                        fn ($q) => $q->where('dealer_id', Auth::user()->dealer_id)
                    )->count() ?: null)
                ->badgeColor('info'),

            'replied' => Tab::make('返信済み')
                ->modifyQueryUsing(fn (Builder $query) => $baseQuery($query)
                    ->where('status', InquiryStatus::REPLIED)),
            'phone_replied' => Tab::make('電話対応済み')
                ->modifyQueryUsing(fn (Builder $query) => $baseQuery($query)
                    ->where('status', InquiryStatus::PHONE_REPLIED)),

            'all' => Tab::make('すべて')
                ->modifyQueryUsing(fn (Builder $query) => $baseQuery($query)),
        ];
    }
}