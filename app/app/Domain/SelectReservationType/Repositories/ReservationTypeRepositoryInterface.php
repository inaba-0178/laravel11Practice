<?php

namespace App\Domain\SelectReservationType\Repositories;

use Illuminate\Support\Collection;

interface ReservationTypeRepositoryInterface
{
    /**
     * ディーラーの許可済み予約種別一覧を取得
     *
     * @param int $dealerId
     * @return Collection
     */
    public function findByDealerId(int $dealerId): Collection;
}