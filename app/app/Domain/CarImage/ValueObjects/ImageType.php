<?php

declare(strict_types=1);

namespace App\Domain\CarImage\ValueObjects;

use InvalidArgumentException;

final class ImageType
{
    private const ALLOWED = ['exterior', 'interior', 'engine', 'other'];

    // 将来的に動画対応する場合はここに追加
    // private const ALLOWED_VIDEO = ['exterior', 'interior', 'engine', 'other'];

    private readonly string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::ALLOWED, true)) {
            throw new InvalidArgumentException(
                "ImageTypeは次のいずれかである必要があります: " . implode(', ', self::ALLOWED)
            );
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}