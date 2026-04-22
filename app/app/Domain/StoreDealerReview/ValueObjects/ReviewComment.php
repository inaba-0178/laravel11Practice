<?php

declare(strict_types=1);

namespace App\Domain\StoreDealerReview\ValueObjects;

use InvalidArgumentException;

final class ReviewComment
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            throw new InvalidArgumentException('クチコミ本文を入力してください。');
        }

        if (mb_strlen($value) > 2000) {
            throw new InvalidArgumentException('クチコミ本文は2000文字以内で入力してください。');
        }

        // HTMLタグを除去（XSS対策）
        $value = strip_tags($value);

        // 制御文字を除去
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}