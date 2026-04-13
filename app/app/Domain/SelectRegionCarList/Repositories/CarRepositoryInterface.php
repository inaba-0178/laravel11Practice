<?php

declare(strict_types=1);

namespace App\Domain\SelectRegionCarList\Repositories;

interface CarRepositoryInterface
{
    public function findByRegionIds(array $regionIds, int $offset, int $limit, array $searchParams = [], string $sortKey = '', string $sortOrder = ''): array;

    public function findTotalCount(array $regionIds, array $searchParams = []): int;
}