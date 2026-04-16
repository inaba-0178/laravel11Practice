<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\SelectDealerStaffData;

use App\Application\UseCases\SelectDealerStaffData\SelectDealerStaffDataUseCase;
use App\Application\UseCases\SelectDealerStaffData\SelectDealerStaffDataInputData;
use App\Domain\SelectDealerStaffData\ValueObjects\DealerId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class SelectDealerStaffDataController extends Controller
{
    public function __construct(
        private readonly SelectDealerStaffDataUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $dealerIdParam = $request->query('dealerId');

            if (empty($dealerIdParam)) {
                return response()->json([
                    'success' => false,
                    'message' => 'dealerIdパラメータが必要です',
                ], 400);
            }

            $input = new SelectDealerStaffDataInputData(
                dealerId: new DealerId($dealerIdParam),
            );

            $output = $this->useCase->execute($input);

            return response()->json($output->toArray());

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (Exception $e) {
            Log::error('SelectDealerStaffData error:', [
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