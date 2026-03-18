<?php
namespace App\Application\UseCases\EquipmentSafety;

use App\Domain\EquipmentSafety\Repositories\EquipmentSafetyRepositoryInterface;

class EquipmentSafetyUseCase
{
    public function __construct(
        private readonly EquipmentSafetyRepositoryInterface $repository,
    ) {}
    public function handle(): EquipmentSafetyOutputData
    {
        $equipmentSafety = $this->repository->getAll();
        return new EquipmentSafetyOutputData($equipmentSafety);
    }
}