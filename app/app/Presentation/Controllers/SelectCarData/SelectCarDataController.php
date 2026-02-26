<?php
namespace App\Presentation\Controllers\SelectCarData;

use App\Application\UseCases\SelectCarData\SelectCarDataUseCase;
use App\Domain\SelectCarData\ValueObjects\CarId;
use App\Domain\SelectCarData\Exceptions\CarNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class SelectCarDataController extends Controller
{
    public function __construct(
       private readonly SelectCarDataUseCase $useCase
    ) {}

    /**
     * 選択した車両一覧データ取得
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $carIdParam = $request->get('carId');
            if (empty($carIdParam)) {
                return response()->json([
                    'success' => false,
                    'message' => 'seriesIdパラメータが必要です',
                ], 400);
            }
            
            $carId = new CarId($carIdParam);
            $outputData = $this->useCase->execute($carId);
            
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
            Log::error('CarData error:', [
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