<?php
namespace App\Application\UseCases\SeatOption;

use Illuminate\Support\Collection;
use App\Domain\SeatOption\Entities\SeatOption;

class SeatOptionOutputData
{
    public function __construct(
        public readonly Collection $seatOptions,
    ) {}
    public function toArray(): array
    {
        return $this->seatOptions
            ->map(fn(SeatOption $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}
