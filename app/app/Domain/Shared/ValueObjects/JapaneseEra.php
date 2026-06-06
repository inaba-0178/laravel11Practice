<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

/**
 * 日本の元号を表すValueObject
 *
 * 西暦から元号に変換する。
 */
final class JapaneseEra
{
    private readonly string $value;

    public function __construct(?int $year)
    {
        if (!$year) {
            $this->value = '-';
            return;
        }

        $this->value = match(true) {
            $year >= 2019 => '令和' . ($year - 2018) . '年',
            $year >= 1989 => '平成' . ($year - 1988) . '年',
            $year >= 1926 => '昭和' . ($year - 1925) . '年',
            default       => (string) $year . '年',
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}