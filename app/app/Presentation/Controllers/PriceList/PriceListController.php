<?php

namespace App\Presentation\Controllers\PriceList;

use App\Application\UseCases\PriceList\PriceListUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;

class PriceListController extends Controller
{
    private PriceListUseCase $useCase;

    public function __construct(PriceListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Region一覧を取得
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