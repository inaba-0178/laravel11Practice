<?php

namespace App\Domain\FavoriteCar\ValueObjects;

use InvalidArgumentException;

final class UserId
{
    private readonly string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('UserIdは空にできません。');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}