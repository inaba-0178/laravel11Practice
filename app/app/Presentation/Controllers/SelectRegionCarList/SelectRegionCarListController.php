<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\SelectRegionCarList;

use App\Application\UseCases\SelectRegionCarList\SelectRegionCarListUseCase;
use App\Domain\SelectRegionCarList\ValueObjects\RegionIds;
use App\Domain\SelectRegionCarList\ValueObjects\OffSet;
use App\Domain\SelectRegionCarList\ValueObjects\Limit;
use App\Domain\SelectRegionCarList\Exceptions\SelectRegionCarNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class SelectRegionCarListController extends Controller
{
    public function __construct(
        private readonly SelectRegionCarListUseCase $useCase
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $offsetParam    = $request->query('offset');
            $limitParam     = $request->query('limit');
            $sortKey        = $request->query('sortKey', '');
            $sortOrder      = $request->query('sortOrder', '');

            $regionIdParam  = $request->query('regionId');
            $regionIdsParam = $request->query('regionIds');

            if (!empty($regionIdsParam)) {
                $ids = explode(',', $regionIdsParam);
            } elseif (!empty($regionIdParam)) {
                $ids = [$regionIdParam];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'regionIdまたはregionIdsパラメータが必要です',
                ], 400);
            }

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
            ]);

            $regionIds  = new RegionIds($ids);
            $offset     = new OffSet((int)($offsetParam ?? 0));
            $limit      = new Limit((int)($limitParam ?? 10));
            $outputData = $this->useCase->execute(
                $regionIds,
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

        } catch (SelectRegionCarNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            Log::error('SelectRegionCarList error:', [
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