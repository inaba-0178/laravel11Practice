<?php

namespace App\Presentation\Controllers\ManufacturerList;

use App\Application\UseCases\ManufacturerList\ManufacturerListUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Domain\ManufacturerList\ValueObjects\ManufacturerIds;
use Exception;

class ManufacturerListController extends Controller
{
    private ManufacturerListUseCase $useCase;

    public function __construct(ManufacturerListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * メーカー一覧データ取得
     * 
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            \Log::info($request->all());
            $manufacturerIds = new ManufacturerIds($request->get('ManufacturerIds'));
            $outputData = $this->useCase->execute($manufacturerIds);
            return response()->json($outputData->toArray());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}