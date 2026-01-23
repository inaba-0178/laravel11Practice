<?php

namespace App\Presentation\Controllers\MileageList;

use App\Application\UseCases\MileageList\MileageListUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;

class MileageListController extends Controller
{
    private MileageListUseCase $useCase;

    public function __construct(MileageListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * 走行距離一覧を取得
     * 
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        try {
            $outputData = $this->useCase->execute();
            return response()->json($outputData->toArray());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}