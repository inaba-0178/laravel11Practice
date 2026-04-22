<?php

declare(strict_types=1);

namespace App\Domain\StoreDealerReview\ValueObjects;

use InvalidArgumentException;

final class PurchasedAt
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        // YYYY/MM形式チェック
        if (!preg_match('/^\d{4}\/(0[1-9]|1[0-2])$/', $value)) {
            throw new InvalidArgumentException('購入年月はYYYY/MM形式で入力してください。例：2026/04');
        }

        // 年の範囲チェック
        [$year] = explode('/', $value);
        if ((int) $year < 1900 || (int) $year > (int) date('Y')) {
            throw new InvalidArgumentException('購入年月の年が不正です。');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}