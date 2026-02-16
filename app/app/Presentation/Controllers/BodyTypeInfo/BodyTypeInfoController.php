<?php
namespace App\Presentation\Controllers\BodyTypeInfo;

use App\Application\UseCases\BodyTypeInfo\BodyTypeInfoUseCase;
use App\Domain\BodyTypeInfo\ValueObjects\BodyTypeName;
use App\Domain\BodyTypeInfo\Exceptions\BodyTypeNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class BodyTypeInfoController extends Controller
{
    public function __construct(
        private readonly BodyTypeInfoUseCase $useCase
    ) {}

    /**
     * 選択したボディタイプデータ取得
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $bodyTypeNameParam = $request->get('bodyTypeName');
            
            if (empty($bodyTypeNameParam)) {
                return response()->json([
                    'success' => false,
                    'message' => 'bodyTypeNameパラメータが必要です',
                ], 400);
            }
            
            $bodyTypeName = new BodyTypeName($bodyTypeNameParam);
            $outputData = $this->useCase->execute($bodyTypeName);
            
            return response()->json($outputData->toArray());
            
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
            
        } catch (BodyTypeNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
            
        } catch (Exception $e) {
            Log::error('BodyType info error:', [
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