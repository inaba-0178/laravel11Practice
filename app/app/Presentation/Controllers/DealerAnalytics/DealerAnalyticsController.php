<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\DealerAnalytics;

use App\Application\UseCases\DealerAnalytics\GetDealerAnalyticsUseCase;
use App\Application\UseCases\DealerAnalytics\GetDealerAnalyticsInputData;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * ディーラー分析データ取得コントローラー
 *
 * GET /api/Analytics/dealer
 */
class DealerAnalyticsController extends Controller
{
    public function __construct(
        private readonly GetDealerAnalyticsUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period'    => 'required|in:weekly,monthly,yearly',
            'base_date' => 'nullable|date',
        ]);

        $dealerId = Auth::user()?->dealer_id;
        if (!$dealerId) {
            return response()->json(['success' => false, 'message' => 'ディーラーIDが見つかりません'], 403);
        }

        $data = new GetDealerAnalyticsInputData(
            dealerId: $dealerId,
            period:   $validated['period'],
            baseDate: isset($validated['base_date'])
                ? Carbon::parse($validated['base_date'])
                : Carbon::now(),
        );

        $result = $this->useCase->execute($data);

        return response()->json([
            'success'      => true,
            'labels'       => $result->labels,
            'viewCounts'   => $result->viewCounts,
            'favCounts'    => $result->favoriteCounts,
            'resCounts'    => $result->reservationCounts,
            'inqCounts'    => $result->inquiryCounts,
            'summary'      => $result->summary,
        ]);
    }
}