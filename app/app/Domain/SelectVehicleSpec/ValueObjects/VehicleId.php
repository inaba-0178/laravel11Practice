<?php

namespace App\Domain\SelectVehicleSpec\ValueObjects;

use InvalidArgumentException;

final class VehicleId
{
    private readonly int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('VehicleIdは正の整数である必要があります。');
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}