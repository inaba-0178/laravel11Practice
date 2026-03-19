<?php

namespace App\Presentation\Controllers\SelectDealerReview;

use App\Application\UseCases\SelectDealerReview\SelectDealerReviewUseCase;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class SelectDealerReviewController extends Controller
{
    public function __construct(
        private readonly SelectDealerReviewUseCase $useCase,
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
            Log::error('DealerReview error:', [
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