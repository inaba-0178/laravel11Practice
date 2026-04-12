<?php

declare(strict_types=1);

namespace App\Domain\SelectRegionCarList\Repositories;

interface CarRepositoryInterface
{
    public function findByRegionId(int $regionId, int $offset, int $limit, array $searchParams = [], string $sortKey = '', string $sortOrder = ''): array;

    public function findTotalCount(int $regionId, array $searchParams = []): int;
}