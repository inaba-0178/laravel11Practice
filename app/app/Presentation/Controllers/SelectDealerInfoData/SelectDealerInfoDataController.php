<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\SelectDealerInfoData;

use App\Application\UseCases\SelectDealerInfoData\SelectDealerInfoDataUseCase;
use App\Application\UseCases\SelectDealerInfoData\SelectDealerInfoDataInputData;
use App\Domain\SelectDealerInfoData\ValueObjects\DealerId;
use App\Domain\SelectDealerInfoData\Exceptions\DealerNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class SelectDealerInfoDataController extends Controller
{
    public function __construct(
        private readonly SelectDealerInfoDataUseCase $useCase,
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

            $input = new SelectDealerInfoDataInputData(
                dealerId: new DealerId($dealerIdParam),
            );

            $output = $this->useCase->execute($input);

            return response()->json($output->toArray());

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (DealerNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            Log::error('SelectDealerInfoData error:', [
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