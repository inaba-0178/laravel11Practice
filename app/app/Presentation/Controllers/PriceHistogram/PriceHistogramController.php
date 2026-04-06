<?php

namespace App\Presentation\Controllers\PriceHistogram;

use App\Application\UseCases\PriceHistogram\PriceHistogramUseCase;
use App\Domain\PriceHistogram\ValueObjects\PriceHistogramCondition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Exception;

class PriceHistogramController extends Controller
{
    public function __construct(
        private readonly PriceHistogramUseCase $useCase
    ) {}

    /**
     * 価格帯ごとの車両件数取得
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $condition = new PriceHistogramCondition(
                manufacturerId:   $request->query('manufacturerId')   ? (int) $request->query('manufacturerId')   : null,
                bodyTypeId:       $request->query('bodyTypeId')       ? (int) $request->query('bodyTypeId')       : null,
                priceFrom:        $request->query('priceFrom')        ? (int) $request->query('priceFrom')        : null,
                priceTo:          $request->query('priceTo')          ? (int) $request->query('priceTo')          : null,
                mileageFrom:      $request->query('mileageFrom')      ? (int) $request->query('mileageFrom')      : null,
                mileageTo:        $request->query('mileageTo')        ? (int) $request->query('mileageTo')        : null,
                ridingCapacity:   $request->query('ridingCapacity')   ? (int) $request->query('ridingCapacity')   : null,
                displacementFrom: $request->query('displacementFrom') ? (int) $request->query('displacementFrom') : null,
                displacementTo:   $request->query('displacementTo')   ? (int) $request->query('displacementTo')   : null,
                regionId:         $request->query('regionId')         ? (int) $request->query('regionId')         : null,
                vehicleId:        $request->query('vehicleId')        ? (int) $request->query('vehicleId')        : null,
            );

            $outputData = $this->useCase->execute($condition);
            return response()->json($outputData->toArray());

        } catch (Exception $e) {
            Log::error('PriceHistogram error:', [
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