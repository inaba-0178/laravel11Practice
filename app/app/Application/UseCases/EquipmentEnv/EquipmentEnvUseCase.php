<?php
namespace App\Application\UseCases\EquipmentEnv;

use App\Domain\EquipmentEnv\Repositories\EquipmentEnvRepositoryInterface;

class EquipmentEnvUseCase
{
    public function __construct(
        private readonly EquipmentEnvRepositoryInterface $repository,
    ) {}
    public function handle(): EquipmentEnvOutputData
    {
        $equipmentEnv = $this->repository->getAll();
        return new EquipmentEnvOutputData($equipmentEnv);
    }
}