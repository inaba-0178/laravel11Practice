<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DealerReservationCalendarWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            DealerReservationCalendarWidget::class,
            // 今後追加するウィジェットはここに追加するだけでOK
            // DealerInfoWidget::class,
            // DealerCarStatusWidget::class,
        ];
    }
}