<?php
namespace App\Domain\SelectCarData\ValueObjects;

use InvalidArgumentException;

final class CarId
{
    private readonly int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('CarIdは正の整数である必要があります。');
        }
        
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}