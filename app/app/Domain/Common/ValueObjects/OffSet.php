<?php

declare(strict_types=1);

namespace App\Domain\Common\ValueObjects;

use InvalidArgumentException;

final class OffSet
{
    private readonly int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('OffSetは0以上の整数である必要があります。');
        }
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}