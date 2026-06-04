<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Analytics;

use App\Application\UseCases\Analytics\RecordViewCountUseCase;
use App\Application\UseCases\Analytics\RecordViewCountInputData;
use App\Domain\Analytics\ValueObjects\CarId;
use App\Domain\Analytics\ValueObjects\DealerId;
use App\Infrastructure\Eloquent\Opr\OprSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * 閲覧数記録コントローラー
 *
 * POST /api/Analytics/recordView
 */
class RecordViewCountController extends Controller
{
    public function __construct(
        private readonly RecordViewCountUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dealer_id' => 'required|integer',
            'car_id'    => 'required|integer',
            'cookie_id' => 'nullable|string|max:255',
        ]);

        $member = $request->user('members');

        try {
            $data = new RecordViewCountInputData(
                dealerId: new DealerId($validated['dealer_id']),
                carId:    new CarId($validated['car_id']),
                memberId: $member?->id,
                cookieId: $validated['cookie_id'] ?? null,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $recorded = $this->useCase->execute($data);

        return response()->json([
            'success'  => true,
            'recorded' => $recorded,
        ]);
    }

    /**
     * 閲覧カウント待機秒数を返す
     *
     * Vue側でタイマーを設定するために使用する。
     * GET /api/Analytics/viewDelay
     */
    public function getDelay(): JsonResponse
    {
        $delay = (int) OprSetting::getValue('view_count_delay_seconds', 5);

        return response()->json(['delay_seconds' => $delay]);
    }
}