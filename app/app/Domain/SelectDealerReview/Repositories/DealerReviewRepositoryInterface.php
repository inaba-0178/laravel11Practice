<?php

namespace App\Domain\SelectDealerReview\Repositories;

use Illuminate\Support\Collection;

interface DealerReviewRepositoryInterface
{
    /**
     * 販売店のクチコミ一覧を取得
     *
     * @param int $dealerId
     * @return Collection
     */
    public function findByDealerId(int $dealerId): Collection;
}