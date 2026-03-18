<?php
namespace App\Application\UseCases\EquipmentDressup;

use App\Domain\EquipmentDressup\Repositories\EquipmentDressupRepositoryInterface;

class EquipmentDressupUseCase
{
    public function __construct(
        private readonly EquipmentDressupRepositoryInterface $repository,
    ) {}
    public function handle(): EquipmentDressupOutputData
    {
        $equipmentDressup = $this->repository->getAll();
        return new EquipmentDressupOutputData($equipmentDressup);
    }
}