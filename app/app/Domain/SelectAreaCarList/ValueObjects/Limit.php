<?php
namespace App\Domain\SelectAreaCarList\ValueObjects;

use InvalidArgumentException;

final class Limit
{
    private readonly int $value;

    public function __construct(int $value)
    {
        if ($value === null) {
            return response()->json([
                'success' => false,
                'message' => 'limitパラメータが必要です',
            ], 400);
        }

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