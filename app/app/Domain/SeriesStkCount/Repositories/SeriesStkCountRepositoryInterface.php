<?php

namespace App\Domain\SeriesStkCount\Repositories;

interface SeriesStkCountRepositoryInterface
{
    public function countBySeriesIds(array $seriesIds): array;
}