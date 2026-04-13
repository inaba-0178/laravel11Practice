<?php

declare(strict_types=1);

namespace App\Domain\SelectRegionCarList\ValueObjects;

use InvalidArgumentException;

final class Limit
{
    private readonly int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Limitは正の整数である必要があります。');
        }
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}