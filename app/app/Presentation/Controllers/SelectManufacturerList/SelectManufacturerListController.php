<?php
namespace App\Presentation\Controllers\SelectManufacturerList;

use App\Application\UseCases\SelectManufacturerList\SelectManufacturerListUseCase;
use App\Domain\SelectManufacturerList\ValueObjects\ManufacturerName;
use App\Domain\SelectManufacturerList\Exceptions\ManufacturerNotFoundException;
use App\Domain\SelectManufacturerList\Exceptions\CarSeriesFetchException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Illuminate\Http\Request; 
use Exception;

class SelectManufacturerListController extends Controller
{
    private SelectManufacturerListUseCase $useCase;

    public function __construct(SelectManufacturerListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * メーカー車種一覧表示データ取得
     * 
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $manufacturerName = new ManufacturerName($request->get('manufacturerName'));
            $outputData = $this->useCase->execute($manufacturerName);
            
            return response()->json($outputData->toArray());
            
        } catch (InvalidArgumentException $e) {
            // バリデーションエラー → 422
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
            
        } catch (ManufacturerNotFoundException $e) {
            // リソース未検出 → 404
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
            
        } catch (CarSeriesFetchException $e) {
            // データ取得エラー → 500
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
            
        } catch (Exception $e) {
            // その他の予期しないエラー → 500
            return response()->json([
                'success' => false,
                'message' => 'サーバーエラーが発生しました。',
            ], 500);
        }
    }
}