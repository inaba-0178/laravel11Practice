<?php

namespace App\Presentation\Controllers\RidingCapacityList;

use App\Application\UseCases\RidingCapacityList\RidingCapacityListUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;

class RidingCapacityListController extends Controller
{
    private RidingCapacityListUseCase $useCase;

    public function __construct(RidingCapacityListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * 乗車定員一覧を取得
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