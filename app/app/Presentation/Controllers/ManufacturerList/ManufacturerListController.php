<?php
namespace App\Presentation\Controllers\ManufacturerList;

use App\Application\UseCases\ManufacturerList\ManufacturerListUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Domain\ManufacturerList\ValueObjects\ManufacturerIds;
use Exception;
use Illuminate\Support\Facades\Log;

class ManufacturerListController extends Controller
{
    public function __construct(
        private readonly ManufacturerListUseCase $useCase
    ) {}

    /**
     * メーカー一覧データ取得
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $ids = $request->get('ManufacturerIds');
            
            if (empty($ids)) {
                // IDなし → 全件取得
                $outputData = $this->useCase->executeAll();
            } else {
                $manufacturerIds = new ManufacturerIds($ids);
                $outputData = $this->useCase->execute($manufacturerIds);
            }
            
            return response()->json($outputData->toArray());
            
        } catch (Exception $e) {
            Log::error('Manufacturer list error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);  // 404ではなく500が適切
        }
    }
}