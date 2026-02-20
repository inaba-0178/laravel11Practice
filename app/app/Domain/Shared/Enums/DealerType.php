<?php

namespace App\Domain\Shared\Enums;

class DealerType
{
    public const NEW_CAR = 'new_car';
    public const USED_CAR = 'used_car';
    public const BOTH = 'both';

    public const LABELS = [
        self::NEW_CAR  => '新車販売',
        self::USED_CAR => '中古車販売',
        self::BOTH     => '新車・中古車両方',
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
     * @return array [['value' => 'new_car', 'label' => '新車販売'], ...]
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