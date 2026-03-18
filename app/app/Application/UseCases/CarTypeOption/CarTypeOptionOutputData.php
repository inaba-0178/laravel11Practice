<?php
namespace App\Application\UseCases\CarTypeOption;

use Illuminate\Support\Collection;
use App\Domain\CarTypeOption\Entities\CarTypeOption;

class CarTypeOptionOutputData
{
    public function __construct(
        public readonly Collection $carTypeOptions,
    ) {}
    public function toArray(): array
    {
        return $this->carTypeOptions
            ->map(fn(CarTypeOption $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}
