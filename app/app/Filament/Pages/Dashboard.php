<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DealerReservationCalendarWidget;
use App\Filament\Widgets\DealerBulkCarStatusWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | string | array
    {
        return 12; // 12カラムグリッドを明示
    }

    public function getWidgets(): array
    {
        return [
            DealerBulkCarStatusWidget::class,      // columnSpan=4
            DealerReservationCalendarWidget::class, // columnSpan=8
        ];
    }
}