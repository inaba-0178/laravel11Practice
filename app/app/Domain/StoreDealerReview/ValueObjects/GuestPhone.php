<?php

declare(strict_types=1);

namespace App\Domain\StoreDealerReview\ValueObjects;

use InvalidArgumentException;

final class GuestPhone
{
    private readonly string $value;

    public function __construct(string $value)
    {
        // ハイフン除去
        $value = str_replace('-', '', trim($value));

        if (empty($value)) {
            throw new InvalidArgumentException('電話番号を入力してください。');
        }

        // 数字のみ・10〜11桁
        if (!preg_match('/^\d{10,11}$/', $value)) {
            throw new InvalidArgumentException('電話番号は10〜11桁の数字で入力してください。');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}