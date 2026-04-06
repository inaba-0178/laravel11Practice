<?php

namespace App\Domain\Common\Constants;

class LoanConstants
{
    /** パーセントを小数に変換 */
    public const ANNUAL_INTEREST_RATE = 100;

    /** 年利を月利に変換（1年12ヶ月） */
    public const MONTHLY_INTEREST = 12;
}