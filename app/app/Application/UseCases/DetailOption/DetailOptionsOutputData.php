<?php

namespace App\Application\UseCases\DetailOption;

use Illuminate\Support\Collection;
use App\Domain\DetailOption\Entities\DetailOption;

class DetailOptionsOutputData
{
    public function __construct(
        public readonly Collection $DetailOptions,
    ) {}

    public function toArray(): array
    {
        return $this->DetailOptions
            ->map(fn(DetailOption $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}