<?php

declare(strict_types=1);

namespace App\Domain\StoreDealerReview\ValueObjects;

use InvalidArgumentException;

final class GuestEmail
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            throw new InvalidArgumentException('メールアドレスを入力してください。');
        }

        if (mb_strlen($value) > 100) {
            throw new InvalidArgumentException('メールアドレスは100文字以内で入力してください。');
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('メールアドレスの形式が正しくありません。');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}