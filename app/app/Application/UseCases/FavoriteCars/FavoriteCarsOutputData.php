<?php

namespace App\Application\UseCases\FavoriteCars;

use Illuminate\Support\Collection;

class FavoriteCarsOutputData
{
    public function __construct(
        private readonly Collection $cars,
    ) {}

    public function toArray(): array
    {
        return [
            'success' => true,
            'cars'    => $this->cars->values()->toArray(),
        ];
    }
}