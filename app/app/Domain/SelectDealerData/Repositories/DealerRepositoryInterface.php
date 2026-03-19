<?php

namespace App\Domain\SelectDealerData\Repositories;

use App\Domain\SelectDealerData\Entities\Dealer;

interface DealerRepositoryInterface
{
    /**
     * 販売店情報を取得
     *
     * @param int $dealerId
     * @return Dealer
     */
    public function findById(int $dealerId): Dealer;
}