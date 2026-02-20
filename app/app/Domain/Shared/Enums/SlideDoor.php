<?php

namespace App\Domain\Shared\Enums;

class SlideDoor
{
    public const NONE = 'none';
    public const RIGHT_ONLY = 'right_only';
    public const BOTH_MANUAL = 'both_manual';
    public const BOTH_POWER = 'both_power';
    public const RIGHT_POWER = 'right_power';
    public const LEFT_POWER = 'left_power';

    public const LABELS = [
        self::NONE         => 'なし',
        self::RIGHT_ONLY   => '右側のみ',
        self::BOTH_MANUAL  => '両側（手動）',
        self::BOTH_POWER   => '両側（電動）',
        self::RIGHT_POWER  => '右側電動',
        self::LEFT_POWER   => '左側電動',
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
     * @return array [['value' => 'none', 'label' => 'なし'], ...]
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