<?php

namespace App\Domain\Shared\Enums;

class OptionCategory
{
    public const BASIC = 'basic';
    public const SAFETY = 'safety';
    public const ENVIRONMENTAL = 'environmental';
    public const AUDIO = 'audio';
    public const NAVIGATION = 'navigation';
    public const SEAT = 'seat';
    public const DRESS_UP = 'dress_up';
    public const OTHER = 'other';

    public const LABELS = [
        self::BASIC          => '基本装備',
        self::SAFETY         => '安全装備・サポート',
        self::ENVIRONMENTAL  => '環境装備',
        self::AUDIO          => 'オーディオ関連',
        self::NAVIGATION     => 'カーナビ/TV/DVD',
        self::SEAT           => 'シート関連',
        self::DRESS_UP       => 'ドレスアップ',
        self::OTHER          => 'その他',
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
     * @return array [['value' => 'basic', 'label' => '基本装備'], ...]
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