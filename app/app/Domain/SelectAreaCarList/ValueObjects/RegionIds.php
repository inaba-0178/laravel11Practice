<?php
namespace App\Domain\SelectAreaCarList\ValueObjects;

use InvalidArgumentException;

final class RegionIds
{
    private readonly array $values;

    public function __construct(array $values)
    {
        if (empty($values)) {
            throw new InvalidArgumentException('RegionIdsは空にできません。');
        }

        foreach ($values as $value) {
            if (!is_int($value)) {
                throw new InvalidArgumentException('RegionIdsは整数である必要があります。');
            }
        }

        $this->values = $values;
    }

    public function getValue(): array
    {
        return $this->values;
    }
}