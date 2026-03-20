<?php

namespace App\Presentation\Controllers\SelectVehicleSpec;

use App\Application\UseCases\SelectVehicleSpec\SelectVehicleSpecUseCase;
use App\Domain\SelectVehicleSpec\ValueObjects\VehicleId;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class SelectVehicleSpecController extends Controller
{
    public function __construct(
        private readonly SelectVehicleSpecUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $vehicleIdParam = $request->query('vehicleId');

            if (empty($vehicleIdParam) || !is_numeric($vehicleIdParam)) {
                return response()->json([
                    'success' => false,
                    'message' => 'vehicleIdパラメータが必要です',
                ], 400);
            }

            $vehicleId  = new VehicleId((int)$vehicleIdParam);
            $outputData = $this->useCase->execute($vehicleId);

            return response()->json($outputData->toArray());

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (Exception $e) {
            Log::error('VehicleSpec error:', [
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