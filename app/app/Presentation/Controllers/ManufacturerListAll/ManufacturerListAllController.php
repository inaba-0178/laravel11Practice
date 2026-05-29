<?php

namespace App\Presentation\Controllers\ManufacturerListAll;

use App\Application\UseCases\ManufacturerListAll\ManufacturerListAllUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ManufacturerListAllController extends Controller
{
    public function __construct(
        private readonly ManufacturerListAllUseCase $useCase
    ) {}

    public function __invoke(): JsonResponse
    {
        try {
            $outputData = $this->useCase->execute();
            return response()->json($outputData->toArray());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}