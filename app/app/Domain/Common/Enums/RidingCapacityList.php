<?php
declare(strict_types=1);
namespace App\Domain\Common\Enums;
use Illuminate\Support\Collection;

enum RidingCapacityList: int {
    case TOW_SEATER     = 1;     // 2人乗り
    case FOUR_SEATER    = 2;     // 4人乗り
    case FIVE_SEATER    = 3;     // 5人乗り
    case SIX_SEATER     = 4;     // 6人乗り
    case SEVEN_SEATER   = 5;     // 7人乗り
    case EIGHT_SEATER   = 6;     // 8人乗り
    case TEN_SEATER     = 7;     // 10人乗り

    public function label(): string {
        return match($this) {
            self::TOW_SEATER    => '2人乗り',
            self::FOUR_SEATER   => '4人乗り',
            self::FIVE_SEATER   => '5人乗り',
            self::SIX_SEATER    => '6人乗り',
            self::SEVEN_SEATER  => '7人乗り',
            self::EIGHT_SEATER  => '8人乗り',
            self::TEN_SEATER    => '10人乗り',
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
