<?php
namespace App\Presentation\Controllers\SelectAreaCarList;

use App\Application\UseCases\SelectAreaCarList\SelectAreaCarListUseCase;
use App\Domain\SelectAreaCarList\ValueObjects\SeriesId;
use App\Domain\SelectAreaCarList\ValueObjects\RegionIds;
use App\Domain\SelectAreaCarList\ValueObjects\OffSet;
use App\Domain\SelectAreaCarList\ValueObjects\Limit;
use App\Domain\SelectAreaCarList\Exceptions\SelectAreaCarNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class SelectAreaCarListController extends Controller
{
    public function __construct(
       private readonly SelectAreaCarListUseCase $useCase
    ) {}

    /**
     * 選択した車両一覧データ取得
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $seriesIdParam  = $request->get('seriesId');
            $regionIdsParam = $request->get('regionIds');
            $offsetParam    = $request->get('offset');
            $limitParam     = $request->get('limit');

            if (empty($seriesIdParam)) {
                return response()->json([
                    'success' => false,
                    'message' => 'seriesIdパラメータが必要です',
                ], 400);
            }
            
            $seriesId   = new SeriesId($seriesIdParam);
            $regionIds  = new RegionIds($regionIdsParam);
            $offset     = new Offset($offsetParam);
            $limit      = new Limit($limitParam);
            $outputData = $this->useCase->execute($seriesId, $regionIds, $offset, $limit);
            
            return response()->json($outputData->toArray());
            
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
            
        } catch (SelectAreaCarNotFoundException $e) {
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