<?php

namespace App\Application\UseCases\DetailOption;

use Illuminate\Support\Collection;
use App\Domain\DetailOption\Entities\DetailOption;

class DetailOptionsOutputData
{
    public function __construct(
        public readonly Collection $detailOptions,
    ) {}

    public function toArray(): array
    {
        return $this->detailOptions
            ->map(fn(DetailOption $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}