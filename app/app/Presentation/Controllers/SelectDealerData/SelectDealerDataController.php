<?php

namespace App\Presentation\Controllers\SelectDealerData;

use App\Application\UseCases\SelectDealerData\SelectDealerDataUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class SelectDealerDataController extends Controller
{
    public function __construct(
        private readonly SelectDealerDataUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $dealerIdParam = $request->query('dealerId');

            if (empty($dealerIdParam) || !is_numeric($dealerIdParam) || (int)$dealerIdParam <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'dealerIdパラメータが必要です',
                ], 400);
            }

            $outputData = $this->useCase->execute((int)$dealerIdParam);

            return response()->json($outputData->toArray());

        } catch (Exception $e) {
            Log::error('DealerData error:', [
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