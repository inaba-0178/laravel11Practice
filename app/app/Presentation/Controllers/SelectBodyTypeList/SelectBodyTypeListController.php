<?php
namespace App\Presentation\Controllers\SelectBodyTypeList;

use App\Application\UseCases\SelectBodyTypeList\SelectBodyTypeListUseCase;
use App\Domain\SelectBodyTypeList\ValueObjects\BodyTypeName;
use App\Domain\SelectBodyTypeList\Exceptions\BodyTypeNotFoundException;
use App\Domain\SelectBodyTypeList\Exceptions\CarSeriesFetchException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Illuminate\Http\Request; 
use Exception;

class SelectBodyTypeListController extends Controller
{
    private SelectBodyTypeListUseCase $useCase;

    public function __construct(SelectBodyTypeListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * ボディタイプ車種一覧表示データ取得
     * 
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $bodyTypeName = new BodyTypeName($request->get('bodyTypeName'));

            $outputData = $this->useCase->execute($bodyTypeName);
            
            return response()->json($outputData->toArray());
            
        } catch (InvalidArgumentException $e) {
            // バリデーションエラー → 422
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
            
        } catch (BodyTypeNotFoundException $e) {
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