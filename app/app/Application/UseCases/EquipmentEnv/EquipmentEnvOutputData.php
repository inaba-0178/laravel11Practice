<?php
namespace App\Application\UseCases\EquipmentEnv;

use Illuminate\Support\Collection;
use App\Domain\EquipmentEnv\Entities\EquipmentEnv;

class EquipmentEnvOutputData
{
    public function __construct(
        public readonly Collection $equipmentEnvs,
    ) {}
    public function toArray(): array
    {
        return $this->equipmentEnvs
            ->map(fn(EquipmentEnv $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}
