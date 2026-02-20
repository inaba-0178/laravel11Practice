<?php

namespace App\Domain\Shared\Enums;

class RepairHistory
{
    public const NONE = 'none';
    public const MINOR = 'minor';
    public const MAJOR = 'major';
    public const UNKNOWN = 'unknown';

    public const LABELS = [
        self::NONE    => '修復歴なし',
        self::MINOR   => '軽微な修復歴あり',
        self::MAJOR   => '修復歴あり',
        self::UNKNOWN => '不明',
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
     * @return array [['value' => 'none', 'label' => '修復歴なし'], ...]
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