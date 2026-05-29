<?php

namespace App\Domain\ManufacturerListAll\Repositories;

interface ManufacturerRepositoryInterface
{
    public function findAllActive(): array;
}