<?php

namespace App\Presentation\Controllers\RegionList;

use App\Application\UseCases\RegionList\RegionListUseCase;
use App\Presentation\Requests\Region\RegionListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;

class RegionListController extends Controller
{
    private RegionListUseCase $useCase;

    public function __construct(RegionListUseCase $useCase)
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