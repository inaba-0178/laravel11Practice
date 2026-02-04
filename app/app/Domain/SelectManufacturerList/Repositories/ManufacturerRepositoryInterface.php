<?php
namespace App\Domain\SelectManufacturerList\Repositories;

interface ManufacturerRepositoryInterface
{
    public function findByName(string $name): ?int;
}