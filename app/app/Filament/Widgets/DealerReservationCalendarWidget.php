<?php

namespace App\Filament\Widgets;

use App\Infrastructure\Eloquent\User\StkReservation;
use App\Infrastructure\Eloquent\User\StkDealerSchedule;
use App\Infrastructure\Eloquent\User\StkDealerReservationTypes;
use App\Infrastructure\Eloquent\Opr\OprReservationTypes;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Constants\ReservationStatus;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class DealerReservationCalendarWidget extends Widget
{
    protected static string $view = 'filament.widgets.dealer-reservation-calendar-widget';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 8;
    protected int | string | array $columnStart = [];

    public static function canView(): bool
    {
        return Auth::user()?->dealer_id !== null;
    }

    // カレンダー表示用
    public int   $displayYear;
    public int   $displayMonth;
    public array $calendarData = [];

    // モーダル用
    public bool   $showModal     = false;
    public string $modalDate     = '';
    public array  $modalReservations = [];
    public array  $expandedIds   = [];

    public function mount(): void
    {
        $today             = now();
        $this->displayYear  = (int)$today->format('Y');
        $this->displayMonth = (int)$today->format('m');
        $this->loadCalendar();
    }

    public function prevMonth(): void
    {
        if ($this->displayMonth === 1) {
            $this->displayMonth = 12;
            $this->displayYear--;
        } else {
            $this->displayMonth--;
        }
        $this->loadCalendar();
    }

    public function nextMonth(): void
    {
        if ($this->displayMonth === 12) {
            $this->displayMonth = 1;
            $this->displayYear++;
        } else {
            $this->displayMonth++;
        }
        $this->loadCalendar();
    }

    public function loadCalendar(): void
    {
        $dealerId = Auth::user()->dealer_id;
        $dateFrom = Carbon::create($this->displayYear, $this->displayMonth, 1)->format('Y-m-d');
        $dateTo   = Carbon::create($this->displayYear, $this->displayMonth, 1)->endOfMonth()->format('Y-m-d');

        // スケジュール取得（定休日含む）
        $schedules = StkDealerSchedule::where('dealer_id', $dealerId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->whereNull('deleted_at')
            ->get()
            ->groupBy(fn($s) => $s->date->format('Y-m-d'));

        // 予約取得
        $reservations = StkReservation::with(['schedule', 'reservationType'])
            ->where('dealer_id', $dealerId)
            ->whereHas('schedule', fn($q) => $q->whereBetween('date', [$dateFrom, $dateTo]))
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNull('deleted_at')
            ->get()
            ->groupBy(fn($r) => $r->schedule->date->format('Y-m-d'));

        // ディーラーの予約種別
        $typeIds = StkDealerReservationTypes::where('dealer_id', $dealerId)
            ->where('is_active', 1)
            ->pluck('reservation_type_id')
            ->toArray();

        $types = OprReservationTypes::whereIn('id', $typeIds)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get(['id', 'name'])
            ->keyBy('id');

        $this->calendarData = [];

        // 全日付分まとめる
        $period = \Carbon\CarbonPeriod::create($dateFrom, $dateTo);
        foreach ($period as $date) {
            $dateStr      = $date->format('Y-m-d');
            $daySchedules = $schedules->get($dateStr, collect());
            $dayReservations = $reservations->get($dateStr, collect());

            $isClosed = $daySchedules->contains('is_closed', true);

            // 種別別件数
            $byType = [];
            foreach ($types as $typeId => $type) {
                $count = $dayReservations->where('reservation_type_id', $typeId)->count();
                if ($count > 0) {
                    $byType[] = [
                        'name'  => $type->name,
                        'count' => $count,
                    ];
                }
            }

            $this->calendarData[$dateStr] = [
                'isClosed'    => $isClosed,
                'hasSchedule' => $daySchedules->isNotEmpty(),
                'totalCount'  => $dayReservations->count(),
                'byType'      => $byType,
            ];
        }
    }

    public function openModal(string $date): void
    {
        $dealerId          = Auth::user()->dealer_id;
        $this->modalDate   = $date;
        $this->expandedIds = [];

        $reservations = StkReservation::with(['schedule', 'reservationType'])
            ->where('dealer_id', $dealerId)
            ->whereHas('schedule', fn($q) => $q->where('date', $date))
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNull('deleted_at')
            ->orderBy('created_at')
            ->get();

        $this->modalReservations = $reservations->map(function ($r) {
            $guestName = $r->guest_name ?? ($r->member?->full_name ?? '不明');
            $carName   = '---';
            if ($r->car) {
                $series  = MstCarSeries::find($r->car->series_id);
                $carName = $series?->series_name ?? '---';
            }

            return [
                'id'           => $r->id,
                'typeName'     => $r->reservationType?->name ?? '---',
                'timeFrom'     => substr($r->schedule?->time_from ?? '', 0, 5),
                'timeTo'       => substr($r->schedule?->time_to ?? '', 0, 5),
                'date'         => $r->schedule?->date->format('Y-m-d') ?? '',
                'guestName'    => $guestName,
                'status'       => $r->status,
                'statusLabel'  => ReservationStatus::labelFromValue($r->status),
                'statusColor'  => ReservationStatus::colorFromValue($r->status),
                'carName'      => $carName,
                'stockNumber'  => $r->car?->stock_number ?? '---',
                'memo'         => $r->memo ?? '',
            ];
        })->toArray();

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function toggleExpand(int $id): void
    {
        if (in_array($id, $this->expandedIds)) {
            $this->expandedIds = array_filter($this->expandedIds, fn($i) => $i !== $id);
        } else {
            $this->expandedIds[] = $id;
        }
    }

    public function getCalendarCells(): array
    {
        $firstDay    = Carbon::create($this->displayYear, $this->displayMonth, 1);
        $totalDays   = $firstDay->daysInMonth;
        $today       = now()->format('Y-m-d');
        $startOffset = $firstDay->dayOfWeek === 0 ? 6 : $firstDay->dayOfWeek - 1;

        $cells = [];
        for ($i = 0; $i < $startOffset; $i++) {
            $cells[] = ['empty' => true];
        }

        for ($day = 1; $day <= $totalDays; $day++) {
            $dateStr = Carbon::create($this->displayYear, $this->displayMonth, $day)->format('Y-m-d');
            $data    = $this->calendarData[$dateStr] ?? [];

            $cells[] = [
                'empty'       => false,
                'day'         => $day,
                'date'        => $dateStr,
                'isToday'     => $dateStr === $today,
                'isPast'      => $dateStr < $today,
                'isClosed'    => $data['isClosed']    ?? false,
                'hasSchedule' => $data['hasSchedule'] ?? false,
                'totalCount'  => $data['totalCount']  ?? 0,
                'byType'      => $data['byType']      ?? [],
            ];
        }

        return $cells;
    }

    public function getWeekdayLabels(): array
    {
        return ['月', '火', '水', '木', '金', '土', '日'];
    }
}