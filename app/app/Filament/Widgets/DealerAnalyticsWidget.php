<?php

namespace App\Filament\Widgets;

use App\Application\UseCases\DealerAnalytics\GetDealerAnalyticsUseCase;
use App\Application\UseCases\DealerAnalytics\GetDealerAnalyticsInputData;
use App\Constants\DashboardLayout;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class DealerAnalyticsWidget extends Widget
{
    protected static string $view = 'filament.widgets.dealer-analytics-widget';
    protected static ?int   $sort = DashboardLayout::SORT_ANALYTICS;

    public static function canView(): bool
    {
        return Auth::user()?->dealer_id !== null;
    }

    public function getColumnSpan(): int | string | array
    {
        return DashboardLayout::SPAN_ANALYTICS;
    }

    public function getColumnStart(): int | string | array
    {
        return DashboardLayout::START_ANALYTICS;
    }

    // 期間切り替え
    public string $period = 'weekly';

    // ドラッグで前後期間移動用のオフセット
    public int $offset = 0;

    // 問い合わせ切り替え（all/reservation/inquiry）
    public string $inquiryMode = 'all';

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        $this->offset = 0;
    }

    public function setInquiryMode(string $mode): void
    {
        $this->inquiryMode = $mode;
    }

    public function prevPeriod(): void
    {
        $this->offset--;
    }

    public function nextPeriod(): void
    {
        if ($this->offset < 0) {
            $this->offset++;
        }
    }

    public function getBaseDate(): Carbon
    {
        $base = Carbon::now();

        return match($this->period) {
            'weekly'  => $base->addDays($this->offset * 7),
            'monthly' => $base->addDays($this->offset * 30),
            'yearly'  => $base->addMonths($this->offset * 12),
            default   => $base,
        };
    }

    public function getAnalytics(): array
    {
        $dealerId = Auth::user()->dealer_id;

        $useCase = app(GetDealerAnalyticsUseCase::class);
        $result  = $useCase->execute(new GetDealerAnalyticsInputData(
            dealerId: $dealerId,
            period:   $this->period,
            baseDate: $this->getBaseDate(),
        ));

        return [
            'labels'       => $result->labels,
            'viewCounts'   => $result->viewCounts,
            'favCounts'    => $result->favoriteCounts,
            'resCounts'    => $result->reservationCounts,
            'inqCounts'    => $result->inquiryCounts,
            'summary'      => $result->summary,
        ];
    }

    public function getPeriodLabel(): string
    {
        return match($this->period) {
            'weekly'  => '週間',
            'monthly' => '月間',
            'yearly'  => '年間',
            default   => '週間',
        };
    }
}