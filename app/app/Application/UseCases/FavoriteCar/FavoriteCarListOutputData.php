<?php

namespace App\Application\UseCases\FavoriteCar;

use Illuminate\Support\Collection;

class FavoriteCarListOutputData
{
    public function __construct(
        private readonly Collection $favorites,
    ) {}

    public function toArray(): array
    {
        return [
            'success'   => true,
            'favorites' => $this->favorites->map(fn ($favorite) => [
                'id'         => $favorite->id,
                'car_id'     => $favorite->car_id,
                'created_at' => $favorite->created_at,
            ])->values()->toArray(),
        ];
    }
}