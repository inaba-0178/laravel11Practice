<?php
namespace App\Presentation\Controllers\CarList;

use App\Application\UseCases\CarList\CarListUseCase;
use App\Domain\CarList\ValueObjects\SeriesId;
use App\Domain\CarList\Exceptions\CarNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CarListController extends Controller
{
    public function __construct(
       private readonly CarListUseCase $useCase
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
            
        } catch (CarNotFoundException $e) {
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