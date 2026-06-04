<?php
declare(strict_types=1);
namespace App\Domain\Inquiry\ValueObjects;
use InvalidArgumentException;

final class CarId
{
    private readonly int $value;
    public function __construct(int|string $value)
    {
        $intValue = (int) $value;
        if ($intValue <= 0) {
            throw new InvalidArgumentException('CarIdは正の整数である必要があります。');
        }
        $this->value = $intValue;
    }
    public function getValue(): int { return $this->value; }
}