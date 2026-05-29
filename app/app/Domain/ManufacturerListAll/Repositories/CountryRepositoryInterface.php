<?php

namespace App\Domain\ManufacturerListAll\Repositories;

interface CountryRepositoryInterface
{
    public function findAllActive(): array;
}