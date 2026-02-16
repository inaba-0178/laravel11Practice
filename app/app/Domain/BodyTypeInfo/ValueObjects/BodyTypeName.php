<?php
namespace App\Domain\BodyTypeInfo\ValueObjects;

use InvalidArgumentException;

final class BodyTypeName
{
    private readonly string $value;

    public function __construct(string $value)  // ← mixed を削除
    {
        if ($value === '') {
            throw new InvalidArgumentException('BodyTypeNameは必須です。');
        }
        
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}