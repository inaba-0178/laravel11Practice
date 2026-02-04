<?php
declare(strict_types=1);
namespace App\Domain\Common\Enums;

enum TransmissionType: string {
    //駆動方式　トランスミッション
    case AT     = 'AT';     // AT
    case MT     = 'MT';     // MT
    case CVT    = 'CVT';    // CVT

    public function label(): string {
        return match($this) {
            self::AT    => 'AT',
            self::MT    => 'MT',
            self::CVT   => 'CVT',
        };
    }
}