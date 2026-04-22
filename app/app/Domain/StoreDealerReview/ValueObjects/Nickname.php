<?php

declare(strict_types=1);

namespace App\Domain\StoreDealerReview\ValueObjects;

use InvalidArgumentException;

final class Nickname
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            throw new InvalidArgumentException('ニックネームを入力してください。');
        }

        if (mb_strlen($value) > 20) {
            throw new InvalidArgumentException('ニックネームは20文字以内で入力してください。');
        }

        // 制御文字を除去
        if (preg_match('/[\x00-\x1F\x7F]/', $value)) {
            throw new InvalidArgumentException('ニックネームに使用できない文字が含まれています。');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}