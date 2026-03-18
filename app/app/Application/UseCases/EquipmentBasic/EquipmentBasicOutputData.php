<?php
namespace App\Application\UseCases\EquipmentBasic;

use Illuminate\Support\Collection;
use App\Domain\EquipmentBasic\Entities\EquipmentBasic;

class EquipmentBasicOutputData
{
    public function __construct(
        public readonly Collection $equipmentBasics,
    ) {}
    public function toArray(): array
    {
        return $this->equipmentBasics
            ->map(fn(EquipmentBasic $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}
