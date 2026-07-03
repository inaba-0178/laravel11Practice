<?php
namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Infrastructure\Eloquent\User\StkReservation;
use Illuminate\Support\Facades\Auth;

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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = Auth::user();

        $dealerId = $user->getEffectiveDealerId();
        if ($dealerId) {
            $query->where('stk_reservations.dealer_id', $dealerId);
        }

        return $query->with(['schedule', 'car', 'member', 'dealer']);
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

            'past' => Tab::make('過去の受付予約')
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