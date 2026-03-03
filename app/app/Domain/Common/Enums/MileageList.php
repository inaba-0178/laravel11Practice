<?php
declare(strict_types=1);
namespace App\Domain\Common\Enums;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

enum MileageList: int {
    case OVER_10K       = 10000;    // 1万km以上（下限値）
    case RANGE_1_3M     = 29999;    // 1〜3万km（上限値）
    case RANGE_3_5M     = 49999;    // 3〜5万km（上限値）
    case RANGE_5_10M    = 99999;    // 5〜10万km（上限値）
    case RANGE_10_15M   = 149999;   // 10〜15万km（上限値）
    case OVER_15M       = 150000;   // 15万km（下限値）

    public function label(): string {
        return match($this) {
            self::OVER_10K      => '1万km以上',
            self::RANGE_1_3M    => '1〜3万km',
            self::RANGE_3_5M    => '3〜5万km',
            self::RANGE_5_10M   => '5〜10万km',
            self::RANGE_10_15M  => '10〜15万km',
            self::OVER_15M      => '15万km',
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

    public function applyQuery(Builder $query): Builder
    {
        return match($this) {
            self::OVER_10K, self::OVER_15M => $query->whereNull('max_amount')
                                                    ->where('min_amount', $this->value),
            default => $query->where('max_amount', $this->value),
        };
    }
}
