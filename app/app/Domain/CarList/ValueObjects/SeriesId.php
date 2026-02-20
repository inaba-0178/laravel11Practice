<?php
namespace App\Domain\CarList\ValueObjects;

use InvalidArgumentException;

final class SeriesId
{
    private readonly int $value;

    public function __construct(int $value)
    {
        if ($value === '') {
            throw new InvalidArgumentException('SeriesIdは必須です。');
        }
        
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}