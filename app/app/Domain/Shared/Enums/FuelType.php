<?php

namespace App\Domain\Shared\Enums;

class FuelType
{
    public const GASOLINE = 'gasoline';
    public const DIESEL = 'diesel';
    public const HYBRID = 'hybrid';
    public const ELECTRIC = 'electric';
    public const PHEV = 'phev';
    public const OTHER = 'other';

    public const LABELS = [
        self::GASOLINE => 'ガソリン',
        self::DIESEL   => 'ディーゼル',
        self::HYBRID   => 'ハイブリッド',
        self::ELECTRIC => '電気（EV）',
        self::PHEV     => 'プラグインハイブリッド（PHEV）',
        self::OTHER    => 'その他',
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
     * @return array [['value' => 'gasoline', 'label' => 'ガソリン'], ...]
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