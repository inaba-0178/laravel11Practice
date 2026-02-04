<?php
declare(strict_types=1);
namespace App\Domain\Common\Enums;

enum ProductionStatus: string {
    //生産状況
    case ACTIVE         = 'active';         // 生産中（現在販売・運用可能な現行車種）
    case DISCONTINUED   = 'discontinued';   // 廃盤・生産終了（カタログ落ちだが中古流通あり）
    case CONCEPT        = 'concept';        // コンセプトカー（市販前・ショーカー・プロトタイプ）

    public function label(): string {
        return match($this) {
            self::ACTIVE        => '生産中',
            self::DISCONTINUED  => '廃盤・生産終了',
            self::CONCEPT       => 'コンセプトカー',
        };
    }
}