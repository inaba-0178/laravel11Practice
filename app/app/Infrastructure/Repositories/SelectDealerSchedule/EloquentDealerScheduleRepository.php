<?php

namespace App\Infrastructure\Repositories\SelectDealerSchedule;

use App\Domain\SelectDealerSchedule\Repositories\DealerScheduleRepositoryInterface;
use App\Domain\SelectDealerSchedule\Entities\DealerSchedule;
use App\Infrastructure\Eloquent\User\StkDealerSchedule;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class EloquentDealerScheduleRepository extends BaseRepository implements DealerScheduleRepositoryInterface
{
    public function __construct(StkDealerSchedule $model)
    {
        parent::__construct($model);
    }

    public function findByDealerAndMonth(int $dealerId, int $reservationTypeId, string $month): Collection
    {
        // 当月の予約数を集計してスケジュールと合わせて取得
        $schedules = $this->model
            ->select([
                'stk_dealer_schedules.*',
                DB::raw('COUNT(stk_reservations.id) as current_reservations'),
            ])
            ->leftJoin('stk_reservations', function ($join) {
                $join->on('stk_reservations.schedule_id', '=', 'stk_dealer_schedules.id')
                    ->whereIn('stk_reservations.status', ['pending', 'confirmed'])
                    ->whereNull('stk_reservations.deleted_at');
            })
            ->where('stk_dealer_schedules.dealer_id', $dealerId)
            ->where('stk_dealer_schedules.reservation_type_id', $reservationTypeId)
            ->where('stk_dealer_schedules.is_available', 1)
            ->whereNot('stk_dealer_schedules.is_closed', 1)
            ->whereRaw("DATE_FORMAT(stk_dealer_schedules.date, '%Y-%m') = ?", [$month])
            ->where('stk_dealer_schedules.date', '>=', now()->toDateString())
            ->groupBy(
                'stk_dealer_schedules.id',
                'stk_dealer_schedules.dealer_id',
                'stk_dealer_schedules.reservation_type_id',
                'stk_dealer_schedules.date',
                'stk_dealer_schedules.time_from',
                'stk_dealer_schedules.time_to',
                'stk_dealer_schedules.max_reservations',
                'stk_dealer_schedules.is_available',
                'stk_dealer_schedules.is_closed',
                'stk_dealer_schedules.created_at',
                'stk_dealer_schedules.updated_at',
                'stk_dealer_schedules.deleted_at',
                'stk_dealer_schedules.delete_reason',
            )
            ->orderBy('stk_dealer_schedules.date')
            ->orderBy('stk_dealer_schedules.time_from')
            ->get();

        return $schedules->map(fn($model) => $this->toEntity($model));
    }

    private function toEntity(StkDealerSchedule $model): DealerSchedule
    {
        return new DealerSchedule(
            id                  : $model->id,
            dealerId            : $model->dealer_id,
            reservationTypeId   : $model->reservation_type_id,
            date                : Carbon::parse($model->date)->format('Y-m-d'),
            timeFrom            : $model->time_from,
            timeTo              : $model->time_to,
            maxReservations     : $model->max_reservations,
            currentReservations : (int)($model->current_reservations ?? 0),
            isAvailable         : $model->is_available,
        );
    }
}