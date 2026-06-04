<?php

declare(strict_types=1);

namespace App\Application\UseCases\Analytics;

use App\Domain\Analytics\ValueObjects\CarId;
use App\Domain\Analytics\ValueObjects\DealerId;

/**
 * 閲覧数記録ユースケースへの入力データ
 */
final class RecordViewCountInputData
{
    public function __construct(
        /** ディーラーID */
        public readonly DealerId $dealerId,

        /** 車両ID */
        public readonly CarId $carId,

        /** 会員ID（未ログイン時はnull） */
        public readonly ?string $memberId,

        /** Cookie識別子（未ログイン時に使用） */
        public readonly ?string $cookieId,
    ) {}
}