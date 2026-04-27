<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\SelectDealerList;

use App\Application\UseCases\SelectDealerList\SelectDealerListUseCase;
use App\Application\UseCases\SelectDealerList\SelectDealerListInputData;
use App\Domain\SelectDealerList\ValueObjects\DealerSearchCondition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class SelectDealerListController extends Controller
{
    public function __construct(
        private readonly SelectDealerListUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $input = new SelectDealerListInputData(
                condition: new DealerSearchCondition(
                    areaId   : $request->query('areaId')   ? (int) $request->query('areaId')   : null,
                    regionId : $request->query('regionId') ? (int) $request->query('regionId') : null,
                    name     : $request->query('name')     ? (string) $request->query('name')  : null,
                    page     : $request->query('page')     ? (int) $request->query('page')     : 1,
                    perPage  : 20,
                ),
            );

            $output = $this->useCase->execute($input);

            return response()->json($output->toArray());

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (Exception $e) {
            Log::error('SelectDealerList error:', [
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