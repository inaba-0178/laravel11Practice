<?php
declare(strict_types=1);
namespace App\Constants;

enum ReservationStatus: string
{
    case PENDING                    = 'pending';
    case CONFIRMED                  = 'confirmed';
    case COMPLETED                  = 'completed';
    case NO_SHOW                    = 'no_show';
    case DENIAL                     = 'denial';
    case CANCELLED_DEALER_CAR_SOLD  = 'cancelled_dealer_car_sold';
    case CANCELLED_DEALER_TROUBLE   = 'cancelled_dealer_trouble';
    case CANCELLED_CUSTOMER         = 'cancelled_customer';
    case CANCELLED_BY_SYSTEM        = 'cancelled_by_system';

    public function label(): string
    {
        return match($this) {
            self::PENDING                   => '仮予約',
            self::CONFIRMED                 => '承認済み',
            self::COMPLETED                 => '対応完了',
            self::NO_SHOW                   => '未来店',
            self::DENIAL                    => '否認',
            self::CANCELLED_DEALER_CAR_SOLD => '車両成約キャンセル',
            self::CANCELLED_DEALER_TROUBLE  => 'ディーラー都合キャンセル',
            self::CANCELLED_CUSTOMER        => 'お客様都合キャンセル',
            self::CANCELLED_BY_SYSTEM       => 'システム自動キャンセル', //（承認時の他予約）
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING                   => 'warning',
            self::CONFIRMED                 => 'success',
            self::COMPLETED                 => 'success',
            self::NO_SHOW                   => 'info',
            self::DENIAL                    => 'danger',
            self::CANCELLED_DEALER_CAR_SOLD => 'warning',
            self::CANCELLED_DEALER_TROUBLE  => 'danger',
            self::CANCELLED_CUSTOMER        => 'gray',
            self::CANCELLED_BY_SYSTEM       => 'gray',
        };
    }

    public static function labelFromValue(string $value): string
    {
        return self::from($value)->label();
    }

    public static function colorFromValue(string $value): string
    {
        return self::from($value)->color();
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}