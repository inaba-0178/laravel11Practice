<?php
declare(strict_types=1);
namespace App\Constants;

enum ReservationStatus: string
{
    case PENDING   = 'pending';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case NO_SHOW   = 'no_show';
    case CANCELLED = 'cancelled';
    case DENIAL    = 'denial';

    public function label(): string
    {
        return match($this) {
            self::PENDING   => '仮予約',
            self::CONFIRMED => '承認済み',
            self::COMPLETED => '対応完了',
            self::NO_SHOW   => '未来店',
            self::CANCELLED => 'キャンセル',
            self::DENIAL    => '否認',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING   => 'warning',
            self::CONFIRMED => 'success',
            self::COMPLETED => 'info',
            self::NO_SHOW   => 'danger',
            self::CANCELLED => 'gray',
            self::DENIAL    => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function labelFromValue(string $value): string
    {
        return self::from($value)->label();
    }

    public static function colorFromValue(string $value): string
    {
        return self::from($value)->color();
    }
}