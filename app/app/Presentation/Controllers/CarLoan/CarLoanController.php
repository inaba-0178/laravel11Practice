<?php

namespace App\Presentation\Controllers\CarLoan;

use App\Application\UseCases\CarLoan\CarLoanUseCase;
use App\Domain\CarLoan\ValueObjects\CarId;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class CarLoanController extends Controller
{
    public function __construct(
        private readonly CarLoanUseCase $useCase
    ) {}

    /**
     * 車両のローン情報取得
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $carIdParam = $request->query('carId');
            if (empty($carIdParam)) {
                return response()->json([
                    'success' => false,
                    'message' => 'carIdパラメータが必要です',
                ], 400);
            }

            $carId = new CarId((int) $carIdParam);
            $outputData = $this->useCase->execute($carId);

            return response()->json($outputData->toArray());

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (Exception $e) {
            Log::error('CarLoan error:', [
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