<?php
declare(strict_types=1);
namespace App\Domain\Common\Enums;
use Illuminate\Support\Collection;

enum AreaCode: int {
    case HOKKAIDO   = 1;     // 北海道
    case TOUHOKU    = 2;     // 東北
    case KANTOU     = 3;     // 関東
    case KANSAI     = 4;     // 関西
    case SIKOKU     = 5;     // 四国
    case HOKURIKU   = 6;     // 北陸・甲信越
    case TOUKAI     = 7;     // 東海
    case TYUUGOKU   = 8;     // 中国
    case KYUUSYU    = 9;     // 九州
    case OKINAWA    = 10;    // 沖縄

    public function label(): string {
        return match($this) {
            self::HOKKAIDO  => '北海道',
            self::TOUHOKU   => '東北',
            self::KANTOU    => '関東',
            self::KANSAI    => '関西',
            self::SIKOKU    => '四国',
            self::HOKURIKU  => '北陸・甲信越',
            self::TOUKAI    => '東海',
            self::TYUUGOKU  => '中国',
            self::KYUUSYU   => '九州',
            self::OKINAWA   => '沖縄',
        };
    }

    public static function labels() : Collection
    {
        $cases = self::cases();
        $labels = collect();
        foreach ($cases as $case) {
            $labels->put($case->value, $case->label());
        }
        return $labels;
    }
}
