<?php

namespace App\Domain\Shared\Enums;

class Transmission
{
    public const AT = 'AT';
    public const MT = 'MT';
    public const CVT = 'CVT';
    public const DCT = 'DCT';
    public const OTHER = 'other';

    public const LABELS = [
        self::AT    => 'オートマ（AT）',
        self::MT    => 'マニュアル（MT）',
        self::CVT   => '無段変速機（CVT）',
        self::DCT   => 'デュアルクラッチ（DCT）',
        self::OTHER => 'その他',
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
     * @return array [['value' => 'AT', 'label' => 'オートマ（AT）'], ...]
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