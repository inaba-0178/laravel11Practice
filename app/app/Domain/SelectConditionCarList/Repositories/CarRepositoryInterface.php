<?php

declare(strict_types=1);

namespace App\Domain\SelectConditionCarList\Repositories;

interface CarRepositoryInterface
{
    public function findByCondition(int $offset, int $limit, array $searchParams = [], string $sortKey = '', string $sortOrder = ''): array;

    public function findTotalCount(array $searchParams = []): int;
}