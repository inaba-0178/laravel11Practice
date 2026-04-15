<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\SelectConditionCarList;

use App\Application\UseCases\SelectConditionCarList\SelectConditionCarListUseCase;
use App\Domain\Common\ValueObjects\OffSet;
use App\Domain\Common\ValueObjects\Limit;
use App\Domain\SelectConditionCarList\Exceptions\SelectConditionCarNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class SelectConditionCarListController extends Controller
{
    public function __construct(
        private readonly SelectConditionCarListUseCase $useCase
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $offsetParam  = $request->query('offset');
            $limitParam   = $request->query('limit');
            $sortKey      = $request->query('sortKey', '');
            $sortOrder    = $request->query('sortOrder', '');

            $searchParams = $request->only([
                'priceFrom',
                'priceTo',
                'yearFrom',
                'yearTo',
                'mileageFrom',
                'mileageTo',
                'transmission',
                'engineType',
                'colors',
                'options',
                'carTypes',
                'engineFrom',
                'engineTo',
                'driveType',
                'handle',
                'doorCount',
                'slideDoor',
                'passengerCount',
                'inspectionRemaining',
                'freeWord',
                'equipment',
                'dealerId',
            ]);

            $offset     = new OffSet((int)($offsetParam ?? 0));
            $limit      = new Limit((int)($limitParam ?? 10));
            $outputData = $this->useCase->execute(
                $offset,
                $limit,
                $searchParams,
                $sortKey,
                $sortOrder,
            );

            return response()->json($outputData->toArray());

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (SelectConditionCarNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            Log::error('SelectConditionCarList error:', [
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