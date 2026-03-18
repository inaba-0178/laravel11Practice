<?php
namespace App\Application\UseCases\EquipmentBasic;

use App\Domain\EquipmentBasic\Repositories\EquipmentBasicRepositoryInterface;

class EquipmentBasicUseCase
{
    public function __construct(
        private readonly EquipmentBasicRepositoryInterface $repository,
    ) {}
    public function handle(): EquipmentBasicOutputData
    {
        $equipmentBasic = $this->repository->getAll();
        return new EquipmentBasicOutputData($equipmentBasic);
    }
}