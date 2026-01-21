<?php

namespace App\Presentation\Controllers\GetRegionList;

use App\Application\UseCases\GetRegionList\GetRegionListUseCase;
use App\Presentation\Requests\Region\GetRegionListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;

class GetRegionListController extends Controller
{
    private GetRegionListUseCase $useCase;

    public function __construct(GetRegionListUseCase $useCase)
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

    /**
     * Region一覧を取得　地方ごとに
     * 地方毎に県をまとめる
     * 
     * @return JsonResponse
     */
    public function groupedByArea(): JsonResponse
    {
        try {
            $outputData = $this->useCase->executeGroupedByArea();
            return response()->json($outputData->toArray());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}