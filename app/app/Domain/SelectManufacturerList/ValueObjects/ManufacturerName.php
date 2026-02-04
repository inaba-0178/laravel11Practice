<?php

namespace App\Domain\SelectManufacturerList\ValueObjects;

use InvalidArgumentException;

class ManufacturerName
{
    private string $value;

    public function __construct(mixed $value)
    {
        $this->validate($value);
        $this->value = (string) $value;
    }

    private function validate(mixed $value): void
    {
        if ($value === null || $value === '') {
            throw new InvalidArgumentException('manufacturerNameは必須です。');
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException('manufacturerNameは文字列である必要があります。');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }
}