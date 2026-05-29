<?php

namespace App\Domain\ManufacturerListAll\Repositories;

interface ManufacturerListAllRepositoryInterface
{
    public function findGrouped(): array;
}