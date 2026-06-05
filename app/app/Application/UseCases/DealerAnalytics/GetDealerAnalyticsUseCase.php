<?php

declare(strict_types=1);

namespace App\Application\UseCases\DealerAnalytics;

use App\Infrastructure\Eloquent\User\StkAnalyticsView;
use App\Infrastructure\Eloquent\User\StkReservation;
use App\Infrastructure\Eloquent\User\StkInquiry;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

/**
 * ディーラー分析データ取得ユースケース
 */
class GetDealerAnalyticsUseCase
{
    public function execute(GetDealerAnalyticsInputData $data): GetDealerAnalyticsOutputData
    {
        $dealerId = $data->dealerId;
        $base     = $data->baseDate;

        [$dateFrom, $dateTo, $labels, $format, $groupFormat] = $this->buildPeriod($data->period, $base);

        // 閲覧数
        $views = StkAnalyticsView::where('dealer_id', $dealerId)
            ->whereBetween('viewed_at', [$dateFrom, $dateTo])
            ->selectRaw("DATE_FORMAT(viewed_at, '{$groupFormat}') as label, COUNT(*) as count")
            ->groupBy('label')
            ->pluck('count', 'label')
            ->toArray();

        // お気に入り数（car_idでディーラーの車両を絞る）
        $carIds = DB::connection('user')
            ->table('stk_cars')
            ->where('dealer_id', $dealerId)
            ->pluck('id')
            ->toArray();

        $favorites = DB::connection('user')
            ->table('usr_favorite_cars')
            ->whereIn('car_id', $carIds)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as label, COUNT(*) as count")
            ->groupBy('label')
            ->pluck('count', 'label')
            ->toArray();

        // 予約数
        $reservations = StkReservation::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNull('deleted_at')
            ->selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as label, COUNT(*) as count")
            ->groupBy('label')
            ->pluck('count', 'label')
            ->toArray();

        // 在庫確認・見積数
        $inquiries = StkInquiry::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNull('deleted_at')
            ->selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as label, COUNT(*) as count")
            ->groupBy('label')
            ->pluck('count', 'label')
            ->toArray();

        // ラベルに合わせてデータを整形
        $viewData        = array_map(fn($l) => $views[$l]        ?? 0, $labels);
        $favoriteData    = array_map(fn($l) => $favorites[$l]    ?? 0, $labels);
        $reservationData = array_map(fn($l) => $reservations[$l] ?? 0, $labels);
        $inquiryData     = array_map(fn($l) => $inquiries[$l]    ?? 0, $labels);

        return new GetDealerAnalyticsOutputData(
            labels:            $labels,
            viewCounts:        $viewData,
            favoriteCounts:    $favoriteData,
            reservationCounts: $reservationData,
            inquiryCounts:     $inquiryData,
            summary: [
                'views'        => array_sum($viewData),
                'favorites'    => array_sum($favoriteData),
                'reservations' => array_sum($reservationData),
                'inquiries'    => array_sum($inquiryData),
            ],
        );
    }

    private function buildPeriod(string $period, Carbon $base): array
    {
        return match($period) {
            'weekly' => [
                $base->copy()->subDays(6)->startOfDay(),
                $base->copy()->endOfDay(),
                collect(CarbonPeriod::create($base->copy()->subDays(6), $base))
                    ->map(fn($d) => $d->format('Y-m-d'))
                    ->toArray(),
                '%Y-%m-%d',
                '%Y-%m-%d',
            ],
            'monthly' => [
                $base->copy()->subDays(29)->startOfDay(),
                $base->copy()->endOfDay(),
                collect(CarbonPeriod::create($base->copy()->subDays(29), $base))
                    ->map(fn($d) => $d->format('Y-m-d'))
                    ->toArray(),
                '%Y-%m-%d',
                '%Y-%m-%d',
            ],
            'yearly' => [
                $base->copy()->subMonths(11)->startOfMonth(),
                $base->copy()->endOfMonth(),
                collect(range(0, 11))
                    ->map(fn($i) => $base->copy()->subMonths(11 - $i)->format('Y-m'))
                    ->toArray(),
                '%Y-%m',
                '%Y-%m',
            ],
            default => throw new \InvalidArgumentException('Invalid period'),
        };
    }
}