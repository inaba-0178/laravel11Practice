<?php

namespace App\Application\UseCases\BasicOption;

use Illuminate\Support\Collection;
use App\Domain\BasicOption\Entities\BasicOption;

class BasicOptionsOutputData
{
    public function __construct(
        public readonly Collection $BasicOptions,
    ) {}

    public function toArray(): array
    {
        return $this->BasicOptions
            ->map(fn(BasicOption $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}