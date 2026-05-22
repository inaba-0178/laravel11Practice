<?php
declare(strict_types=1);
namespace App\Constants;

enum ScheduleTimeInterval: int
{
    case FIVE    = 5;
    case TEN     = 10;
    case FIFTEEN = 15;
    case THIRTY  = 30;

    public function label(): string
    {
        return match($this) {
            self::FIVE    => '5分単位',
            self::TEN     => '10分単位',
            self::FIFTEEN => '15分単位',
            self::THIRTY  => '30分単位',
        };
    }

    /**
     * 時間帯セレクトの選択肢
     * 管理ツール　スケジュール管理　見ればわかるがコメント残しておく
     * 間隔は ScheduleTimeInterval::SELECT_TIME で変更可能
     * ディーラーの営業時間が設定されている場合はその範囲に絞る
     */
    const SELECT_TIME = self::FIFTEEN;
}