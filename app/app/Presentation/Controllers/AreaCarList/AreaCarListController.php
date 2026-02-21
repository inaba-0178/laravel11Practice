<?php
namespace App\Presentation\Controllers\AreaCarList;

use App\Application\UseCases\AreaCarList\AreaCarListUseCase;
use App\Domain\AreaCarList\ValueObjects\SeriesId;
use App\Domain\AreaCarList\Exceptions\AreaCarNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AreaCarListController extends Controller
{
    public function __construct(
       private readonly AreaCarListUseCase $useCase
    ) {}

    /**
     * 選択した車両一覧データ取得
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $seriesIdParam = $request->get('seriesId');
            if (empty($seriesIdParam)) {
                return response()->json([
                    'success' => false,
                    'message' => 'seriesIdパラメータが必要です',
                ], 400);
            }
            
            $seriesId = new SeriesId($seriesIdParam);
            $outputData = $this->useCase->execute($seriesId);
            
            return response()->json($outputData->toArray());
            
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
            
        } catch (AreaCarNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
            
        } catch (Exception $e) {
            Log::error('CarList error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'サーバーエラーが発生しました。',
            ], 500);
        }
    }
}