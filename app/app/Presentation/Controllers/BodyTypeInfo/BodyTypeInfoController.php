<?php
namespace App\Presentation\Controllers\BodyTypeInfo;

use App\Application\UseCases\BodyTypeInfo\BodyTypeInfoUseCase;
use App\Domain\BodyTypeInfo\ValueObjects\BodyTypeName;
use App\Domain\BodyTypeInfo\Exceptions\BodyTypeNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Illuminate\Http\Request; 
use Exception;

class BodyTypeInfoController extends Controller
{
    private BodyTypeInfoUseCase $useCase;

    public function __construct(BodyTypeInfoUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * 選択したボディタイプデータ取得
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

        } catch (Exception $e) {
            // その他の予期しないエラー → 500
            return response()->json([
                'success' => false,
                'message' => 'サーバーエラーが発生しました。',
            ], 500);
        }
    }
}