<?php

namespace App\Domain\FavoriteCars\ValueObjects;

use InvalidArgumentException;

final class CarIds
{
    private readonly array $values;

    public function __construct(array $values)
    {
        if (empty($values)) {
            throw new InvalidArgumentException('CarIdsは1件以上必要です。');
        }

        foreach ($values as $value) {
            if (!is_int($value) || $value <= 0) {
                throw new InvalidArgumentException('CarIdsは正の整数の配列である必要があります。');
            }
        }

        $this->values = array_unique($values);
    }

    public function getValues(): array
    {
        return $this->values;
    }
}