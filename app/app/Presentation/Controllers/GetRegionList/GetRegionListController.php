<?php

namespace App\Presentation\Controllers\GetRegionList;

use App\Application\UseCases\GetRegionList\GetRegionListUseCase;
use App\Application\UseCases\GetRegionList\GetRegionListInputData;
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
     * @param GetRegionListRequest $request
     * @return JsonResponse
     */
    public function __invoke(GetRegionListRequest $request): JsonResponse
    {
        try {
            $inputData = new GetRegionListInputData(
                $request->input('active_only', false)
            );

            $outputData = $this->useCase->execute($inputData);

            return response()->json($outputData->toArray());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}