<?php

namespace App\Domain\Shared\Enums;

class PriceDisplayType
{
    public const ACTUAL = 'actual';
    public const NEGOTIABLE = 'negotiable';
    public const ASK = 'ask';

    public const LABELS = [
        self::ACTUAL     => '価格表示',
        self::NEGOTIABLE => '応談',
        self::ASK        => 'ASK（要問合せ）',
    ];

    /**
     * ラベルを取得
     */
    public static function label(string $value): string
    {
        return self::LABELS[$value] ?? $value;
    }

    /**
     * セレクトボックス用の配列を取得
     * @return array [['value' => 'actual', 'label' => '価格表示'], ...]
     */
    public static function selectOptions(): array
    {
        $options = [];
        foreach (self::LABELS as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
            ];
        }
        return $options;
    }

    /**
     * 全ての値を取得
     */
    public static function values(): array
    {
        return array_keys(self::LABELS);
    }

    /**
     * 値が有効かチェック
     */
    public static function isValid(string $value): bool
    {
        return array_key_exists($value, self::LABELS);
    }
}