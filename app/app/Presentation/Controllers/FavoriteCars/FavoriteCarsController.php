<?php

namespace App\Presentation\Controllers\FavoriteCars;

use App\Application\UseCases\FavoriteCars\FavoriteCarsUseCase;
use App\Domain\FavoriteCars\ValueObjects\CarIds;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class FavoriteCarsController extends Controller
{
    public function __construct(
        private readonly FavoriteCarsUseCase $useCase
    ) {}

    /**
     * お気に入り車両情報一括取得
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $rawCarIds = $request->query('carIds', '');

            if (empty($rawCarIds)) {
                return response()->json([
                    'success' => true,
                    'cars'    => [],
                ]);
            }

            // 数値チェック
            $rawArray = explode(',', $rawCarIds);
            $hasInvalid = array_filter($rawArray, fn($v) => !ctype_digit($v) || (int)$v <= 0);

            if (!empty($hasInvalid)) {
                return response()->json([
                    'success' => false,
                    'message' => 'carIdsは正の整数で指定してください。',
                ], 422);
            }

            $carIdsParam = array_map('intval', $rawArray);
            $carIds      = new CarIds($carIdsParam);
            $outputData  = $this->useCase->execute($carIds);

            return response()->json($outputData->toArray());

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (Exception $e) {
            Log::error('FavoriteCars error:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'サーバーエラーが発生しました。',
            ], 500);
        }
    }
}