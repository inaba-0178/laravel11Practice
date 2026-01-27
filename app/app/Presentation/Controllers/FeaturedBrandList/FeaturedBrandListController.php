<?php

namespace App\Presentation\Controllers\FeaturedBrandList;

use App\Application\UseCases\FeaturedBrandList\FeaturedBrandListUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;

class FeaturedBrandListController extends Controller
{
    private FeaturedBrandListUseCase $useCase;

    public function __construct(FeaturedBrandListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Topページのメーカー一覧表示データ取得
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