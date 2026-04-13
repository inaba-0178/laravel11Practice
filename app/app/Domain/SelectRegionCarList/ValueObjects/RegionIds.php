<?php

declare(strict_types=1);

namespace App\Domain\SelectRegionCarList\ValueObjects;

use InvalidArgumentException;

final class RegionIds
{
    private readonly array $values;

    public function __construct(array $values)
    {
        if (empty($values)) {
            throw new InvalidArgumentException('RegionIdsは空にできません。');
        }

        $intValues = [];
        foreach ($values as $value) {
            if (!is_numeric($value)) {
                throw new InvalidArgumentException('RegionIdsは整数である必要があります。');
            }
            $intValues[] = (int)$value;
        }

        $this->values = $intValues;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}