<?php

declare(strict_types=1);

namespace App\Domain\StoreDealerReview\ValueObjects;

use InvalidArgumentException;

final class PurchasedCar
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('購入車種は255文字以内で入力してください。');
        }

        // 制御文字を除去
        if (preg_match('/[\x00-\x1F\x7F]/', $value)) {
            throw new InvalidArgumentException('購入車種に使用できない文字が含まれています。');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}