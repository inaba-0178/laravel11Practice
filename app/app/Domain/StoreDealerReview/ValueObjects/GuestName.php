<?php

declare(strict_types=1);

namespace App\Domain\StoreDealerReview\ValueObjects;

use InvalidArgumentException;

final class GuestName
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            throw new InvalidArgumentException('氏名を入力してください。');
        }

        if (mb_strlen($value) > 20) {
            throw new InvalidArgumentException('氏名は20文字以内で入力してください。');
        }

        // 文字のみ許可（日本語・英字）
        if (!preg_match('/^[\p{L}\p{M}\s]+$/u', $value)) {
            throw new InvalidArgumentException('氏名は文字のみで入力してください。');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}