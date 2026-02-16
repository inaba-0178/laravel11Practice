<?php

namespace App\Domain\BodyTypeInfo\ValueObjects;

use InvalidArgumentException;

class BodyTypeName
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
            throw new InvalidArgumentException('BodyTypeNameは必須です。');
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException('BodyTypeNameは文字列である必要があります。');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }
}