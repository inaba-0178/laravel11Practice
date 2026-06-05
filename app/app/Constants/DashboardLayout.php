<?php

declare(strict_types=1);

namespace App\Constants;

class DashboardLayout
{
    // ソート順
    const SORT_CAR_STATUS       = 1;
    const SORT_BULK_CAR_STATUS  = 2;
    const SORT_CALENDAR         = 3;
    const SORT_ANALYTICS        = 4;

    // カラム幅
    const SPAN_CAR_STATUS       = 4;
    const SPAN_BULK_CAR_STATUS  = 4;
    const SPAN_CALENDAR         = 8;
    const START_CALENDAR        = 5; 
    const SPAN_ANALYTICS        = 8;
    const START_ANALYTICS       = 5;
}