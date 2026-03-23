<?php
namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Infrastructure\Eloquent\User\StkReservation;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return '予約一覧';
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->join('stk_dealer_schedules', 'stk_reservations.schedule_id', '=', 'stk_dealer_schedules.id')
            ->orderBy('stk_dealer_schedules.date', 'asc')
            ->orderBy('stk_dealer_schedules.time_from', 'asc')
            ->select('stk_reservations.*');
    }

    // タブで未来・過去を切り替え
    public function getTabs(): array
    {
        return [
            'upcoming' => Tab::make('受付中')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('schedule', function (Builder $q) {
                    $q->where('date', '>=', now()->toDateString());
                }))
                ->badge(fn() => $this->getUpcomingCount()),

            'past' => Tab::make('過去の予約')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('schedule', function (Builder $q) {
                    $q->where('date', '<', now()->toDateString());
                })),
        ];
    }

    private function getUpcomingCount(): int
    {
        return StkReservation::whereHas('schedule', function (Builder $q) {
            $q->where('date', '>=', now()->toDateString());
        })->whereIn('status', ['pending', 'confirmed'])->count();
    }
}