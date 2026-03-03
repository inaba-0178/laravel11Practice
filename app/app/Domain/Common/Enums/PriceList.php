<?php
declare(strict_types=1);
namespace App\Domain\Common\Enums;
use Illuminate\Support\Collection;

enum PriceList: int {
    case MAX_300K  = 300000;
    case MAX_500K  = 500000;
    case MAX_1M    = 1000000;
    case MAX_2M    = 2000000;
    case MAX_3M    = 3000000;
    case MAX_5M    = 5000000;

    public function label(): string {
        return match($this) {
            self::MAX_300K  => '〜30万円',
            self::MAX_500K  => '〜50万円',
            self::MAX_1M    => '〜100万円',
            self::MAX_2M    => '〜200万円',
            self::MAX_3M    => '〜300万円',
            self::MAX_5M    => '〜500万円以上',
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
