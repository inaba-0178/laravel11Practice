<?php

namespace App\Application\UseCases\SeriesStkCount;

use App\Domain\SeriesStkCount\Repositories\SeriesStkCountRepositoryInterface;
use App\Domain\SeriesStkCount\ValueObjects\SeriesStkCountRequest;

class SeriesStkCountUseCase
{
    public function __construct(
        private readonly SeriesStkCountRepositoryInterface $repository
    ) {}

    public function execute(SeriesStkCountRequest $request): array
    {
        $counts = $this->repository->countBySeriesIds($request->seriesIds);

        $countMap = collect($counts)->keyBy('series_id');

        return array_map(function (int $seriesId) use ($countMap) {
            return [
                'seriesId' => $seriesId,
                'num'      => $countMap->has($seriesId) ? (int) $countMap[$seriesId]['num'] : 0,
            ];
        }, $request->seriesIds);
    }
}