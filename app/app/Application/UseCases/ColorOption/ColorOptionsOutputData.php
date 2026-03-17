<?php

namespace App\Application\UseCases\ColorOption;

use Illuminate\Support\Collection;
use App\Domain\ColorOption\Entities\ColorOption;

class ColorOptionsOutputData
{
    public function __construct(
        public readonly Collection $colorOptions,
    ) {}

    public function toArray(): array
    {
        return $this->colorOptions
            ->map(fn(ColorOption $entity) => $entity->toArray())
            ->values()
            ->toArray();
    }
}