<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DealerReservationCalendarWidget;
use App\Filament\Widgets\DealerBulkCarStatusWidget;
use App\Filament\Widgets\DealerCarStatusWidget;
use App\Filament\Widgets\DealerAnalyticsWidget;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasResourcePermission;
    public function getColumns(): int | string | array
    {
        return 12; // 12カラムグリッドを明示
    }

    public function getWidgets(): array
    {
        return [
            DealerCarStatusWidget::class,           // sort=1, span=4, start=1
            DealerReservationCalendarWidget::class,  // sort=2, span=8, start=5
            DealerBulkCarStatusWidget::class,        // sort=3, span=4, start=1
            DealerAnalyticsWidget::class,
        ];
    }
}