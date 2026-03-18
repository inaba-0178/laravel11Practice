<?php
namespace App\Application\UseCases\SeatOption;

use Illuminate\Support\Collection;
use App\Domain\SeatOption\Entities\SeatOption;

class SeatOptionOutputData
{
    public function __construct(
        public readonly Collection $equipmentSafeties,
    ) {}
    public function toArray(): array
    {
        return $this->equipmentSafeties
            ->map(fn(SeatOption $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}
