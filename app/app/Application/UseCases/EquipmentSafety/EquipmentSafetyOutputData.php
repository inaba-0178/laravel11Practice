<?php
namespace App\Application\UseCases\EquipmentSafety;

use Illuminate\Support\Collection;
use App\Domain\EquipmentSafety\Entities\EquipmentSafety;

class EquipmentSafetyOutputData
{
    public function __construct(
        public readonly Collection $equipmentSafetys,
    ) {}
    public function toArray(): array
    {
        return $this->equipmentSafetys
            ->map(fn(EquipmentSafety $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}
