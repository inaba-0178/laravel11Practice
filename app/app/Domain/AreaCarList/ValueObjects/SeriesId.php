<?php
namespace App\Domain\AreaCarList\ValueObjects;

use InvalidArgumentException;

final class SeriesId
{
    private readonly int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('SeriesIdは正の整数である必要があります。');
        }
        
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}