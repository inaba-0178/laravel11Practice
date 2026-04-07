<?php

namespace App\Presentation\Controllers\FavoriteCar;

use App\Application\UseCases\FavoriteCar\FavoriteCarUseCase;
use App\Domain\FavoriteCar\ValueObjects\CarId;
use App\Domain\FavoriteCar\ValueObjects\UserId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class FavoriteCarController extends Controller
{
    public function __construct(
        private readonly FavoriteCarUseCase $useCase
    ) {}

    /**
     * お気に入り登録・解除トグル
     */
    public function toggle(Request $request): JsonResponse
    {
        try {
            $userId = new UserId($request->user()->id);
            $carId  = new CarId((int) $request->query('carId'));

            $outputData = $this->useCase->toggle($userId, $carId);

            return response()->json($outputData->toArray());

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (Exception $e) {
            Log::error('FavoriteCar toggle error:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'サーバーエラーが発生しました。',
            ], 500);
        }
    }

    /**
     * お気に入り状態確認
     */
    public function isFavorite(Request $request): JsonResponse
    {
        try {
            $userId = new UserId($request->user()->id);
            $carId  = new CarId((int) $request->query('carId'));

            $outputData = $this->useCase->isFavorite($userId, $carId);

            return response()->json($outputData->toArray());

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (Exception $e) {
            Log::error('FavoriteCar isFavorite error:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'サーバーエラーが発生しました。',
            ], 500);
        }
    }

    /**
     * お気に入り一覧取得
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $userId = new UserId($request->user()->id);

            $outputData = $this->useCase->list($userId);

            return response()->json($outputData->toArray());

        } catch (Exception $e) {
            Log::error('FavoriteCar list error:', [
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