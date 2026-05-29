<?php

namespace App\Domain\ManufacturerListAll\Repositories;

interface CarCountRepositoryInterface
{
    public function findAvailableCounts(): array;
}