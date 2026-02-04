<?php
declare(strict_types=1);
namespace App\Domain\Common\Enums;

enum DriveType: string {
    //駆動タイプ
    case FF         = 'FF';        // FF (Front-engine, Front-drive) (前輪駆動)
    case FR         = 'FR';        // FR (Front-engine, Rear-drive) (後輪駆動)
    case TWO_WD     = '2WD';    // 2WD（FF/FR)
    case FOUR_WD    = '4WD';   // 4WD (Four-Wheel Drive) (4輪駆動 / AWD)

    public function label(): string {
        return match($this) {
            self::FF        => 'FF',
            self::FR        => 'FR',
            self::TWO_WD    => '2WD',
            self::FOUR_WD   => '4WD',
        };
    }
}