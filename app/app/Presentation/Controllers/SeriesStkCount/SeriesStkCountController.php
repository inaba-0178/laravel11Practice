<?php

namespace App\Presentation\Controllers\SeriesStkCount;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Application\UseCases\SeriesStkCount\SeriesStkCountUseCase;
use App\Domain\SeriesStkCount\ValueObjects\SeriesStkCountRequest;

class SeriesStkCountController
{
    public function __construct(
        private readonly SeriesStkCountUseCase $useCase
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $seriesStkCountRequest = new SeriesStkCountRequest(
                $request->query('seriesIds', [])
            );

            $result = $this->useCase->execute($seriesStkCountRequest);

            return response()->json([
                'success' => true,
                'data'    => $result,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}