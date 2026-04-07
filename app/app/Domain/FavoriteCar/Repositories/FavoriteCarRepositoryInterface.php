<?php

namespace App\Domain\FavoriteCar\Repositories;

use Illuminate\Support\Collection;

interface FavoriteCarRepositoryInterface
{
    /**
     * お気に入り登録・解除トグル
     *
     * @param  string   $userId
     * @param  int      $carId
     * @return bool     トグル後の状態（true=登録・false=解除）
     */
    public function toggle(string $userId, int $carId): bool;

    /**
     * お気に入り状態確認
     *
     * @param  string   $userId
     * @param  int      $carId
     * @return bool
     */
    public function isFavorite(string $userId, int $carId): bool;

    /**
     * ユーザーのお気に入り一覧取得
     *
     * @param  string $userId
     * @return Collection
     */
    public function findByUserId(string $userId): Collection;
}