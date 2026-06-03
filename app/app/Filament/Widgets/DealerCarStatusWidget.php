<?php

namespace App\Filament\Widgets;

use App\Infrastructure\Eloquent\User\StkCar;
use App\Constants\CarStatus;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Constants\DashboardLayout;

class DealerCarStatusWidget extends Widget
{
    protected static string         $view       = 'filament.widgets.dealer-car-status-widget';
    protected static ?int           $sort       = DashboardLayout::SORT_CAR_STATUS;
    protected int | string | array  $columnSpan = DashboardLayout::SPAN_CAR_STATUS;

    const MAX_DISPLAY    = 100;
    const DISPLAY_HEIGHT = 10;

    public static function canView(): bool
    {
        return Auth::user()?->dealer_id !== null;
    }

    public function getSummary(): array
    {
        $dealerId = Auth::user()->dealer_id;

        $query = StkCar::where('dealer_id', $dealerId)
            ->whereNull('bulk_upload_key')
            ->whereNull('deleted_at');

        return [
            'pending'   => (clone $query)->where('status', CarStatus::PENDING)->count(),
            'available' => (clone $query)->where('status', CarStatus::AVAILABLE)->count(),
            'rejected'  => (clone $query)->where('status', CarStatus::REJECTED)->count(),
        ];
    }

    public function getCars(): array
    {
        $dealerId = Auth::user()->dealer_id;

        return StkCar::with(['series'])
            ->where('dealer_id', $dealerId)
            ->whereNull('bulk_upload_key')
            ->whereNull('deleted_at')
            ->whereIn('status', [
                CarStatus::PENDING,
                CarStatus::AVAILABLE,
                CarStatus::REJECTED,
                CarStatus::APPROVED_PENDING,
            ])
            ->orderBy('updated_at', 'desc')
            ->limit(self::MAX_DISPLAY)
            ->get()
            ->map(fn($car) => [
                'id'         => $car->id,
                'seriesName' => $car->series?->series_name ?? '---',
                'status'     => $car->status,
                'statusLabel'=> CarStatus::LABELS[$car->status] ?? $car->status,
                'updatedAt'  => $car->updated_at?->format('Y/m/d H:i'),
                'createdAt'  => $car->created_at?->format('Y/m/d H:i'),
                'detailUrl'  => route('filament.admin.resources.car-registrations.edit', ['record' => $car->id]),
            ])
            ->toArray();
    }
}