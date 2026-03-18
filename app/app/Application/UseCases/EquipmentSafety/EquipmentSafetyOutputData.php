<?php
namespace App\Application\UseCases\EquipmentSafety;

use Illuminate\Support\Collection;
use App\Domain\EquipmentSafety\Entities\EquipmentSafety;

class EquipmentSafetyOutputData
{
    public function __construct(
        public readonly Collection $equipmentSafeties,
    ) {}
    public function toArray(): array
    {
        return $this->equipmentSafeties
            ->map(fn(EquipmentSafety $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}
