<?php
namespace App\Domain\SelectManufacturerList\Repositories;

use App\Domain\SelectManufacturerList\Entities\Manufacturer;

interface ManufacturerRepositoryInterface
{
    public function findByName(string $name): ?Manufacturer;
}