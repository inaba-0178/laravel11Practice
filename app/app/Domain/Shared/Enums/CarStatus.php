<?php

namespace App\Domain\Shared\Enums;

class CarStatus
{
    public const AVAILABLE = 'available';
    public const RESERVED = 'reserved';
    public const SOLD = 'sold';
    public const DELETED = 'deleted';

    public const LABELS = [
        self::AVAILABLE => '販売中',
        self::RESERVED  => '商談中',
        self::SOLD      => '売約済み',
        self::DELETED   => '削除済み',
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
     * @return array [['value' => 'available', 'label' => '販売中'], ...]
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