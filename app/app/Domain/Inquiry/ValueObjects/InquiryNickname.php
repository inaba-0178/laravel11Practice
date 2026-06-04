<?php

declare(strict_types=1);

namespace App\Domain\Inquiry\ValueObjects;

use InvalidArgumentException;

/**
 * ニックネームを表すValueObject
 *
 * 1文字以上50文字以内であることを保証する。
 */
final class InquiryNickname
{
    private readonly string $value;

    /**
     * @param string $value ニックネーム
     * @throws InvalidArgumentException 空文字または50文字超の場合
     */
    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if (mb_strlen($trimmed) === 0) {
            throw new InvalidArgumentException('ニックネームを入力してください。');
        }

        if (mb_strlen($trimmed) > 50) {
            throw new InvalidArgumentException('ニックネームは50文字以内で入力してください。');
        }

        $this->value = $trimmed;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}