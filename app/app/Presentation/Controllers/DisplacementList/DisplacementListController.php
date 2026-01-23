<?php

namespace App\Presentation\Controllers\DisplacementList;

use App\Application\UseCases\DisplacementList\DisplacementListUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;

class DisplacementListController extends Controller
{
    private DisplacementListUseCase $useCase;

    public function __construct(DisplacementListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * 排気量一覧を取得
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