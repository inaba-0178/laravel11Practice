<?php

namespace App\Application\UseCases\FavoriteCar;

class FavoriteCarOutputData
{
    public function __construct(
        private readonly bool $isFavorite,
    ) {}

    public function toArray(): array
    {
        return [
            'success'     => true,
            'is_favorite' => $this->isFavorite,
        ];
    }
}