<?php
namespace App\Application\UseCases\EquipmentDressup;

use Illuminate\Support\Collection;
use App\Domain\EquipmentDressup\Entities\EquipmentDressup;

class EquipmentDressupOutputData
{
    public function __construct(
        public readonly Collection $equipmentDressups,
    ) {}
    public function toArray(): array
    {
        return $this->equipmentDressups
            ->map(fn(EquipmentDressup $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}
