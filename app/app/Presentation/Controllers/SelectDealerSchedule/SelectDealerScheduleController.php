<?php

namespace App\Presentation\Controllers\SelectDealerSchedule;

use App\Application\UseCases\SelectDealerSchedule\SelectDealerScheduleUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class SelectDealerScheduleController extends Controller
{
    public function __construct(
        private readonly SelectDealerScheduleUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $dealerIdParam          = $request->query('dealerId');
            $reservationTypeIdParam = $request->query('reservationTypeId');
            $monthParam             = $request->query('month');

            if (empty($dealerIdParam) || !is_numeric($dealerIdParam) || (int)$dealerIdParam <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'dealerIdパラメータが必要です',
                ], 400);
            }

            if (empty($reservationTypeIdParam) || !is_numeric($reservationTypeIdParam)) {
                return response()->json([
                    'success' => false,
                    'message' => 'reservationTypeIdパラメータが必要です',
                ], 400);
            }

            if (empty($monthParam) || !preg_match('/^\d{4}-\d{2}$/', $monthParam)) {
                return response()->json([
                    'success' => false,
                    'message' => 'monthパラメータはYYYY-MM形式で指定してください',
                ], 400);
            }

            $outputData = $this->useCase->execute(
                (int)$dealerIdParam,
                (int)$reservationTypeIdParam,
                $monthParam,
            );

            return response()->json($outputData->toArray());

        } catch (Exception $e) {
            Log::error('DealerSchedule error:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'サーバーエラーが発生しました。',
            ], 500);
        }
    }
}