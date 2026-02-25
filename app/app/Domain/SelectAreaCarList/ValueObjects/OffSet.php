<?php
namespace App\Domain\SelectAreaCarList\ValueObjects;

use InvalidArgumentException;

final class OffSet
{
    private readonly int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('OffSetは正の整数である必要があります。');
        }
        
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}