<?php

namespace App\Infrastructure\Repositories\FavoriteCar;

use App\Domain\FavoriteCar\Repositories\FavoriteCarRepositoryInterface;
use App\Infrastructure\Eloquent\User\UsrFavoriteCar;
use Illuminate\Support\Collection;

class EloquentFavoriteCarRepository implements FavoriteCarRepositoryInterface
{
    /**
     * お気に入り登録・解除トグル
     */
    public function toggle(string $userId, int $carId): bool
    {
        $favorite = UsrFavoriteCar::withTrashed()
            ->where('user_id', $userId)
            ->where('car_id', $carId)
            ->first();

        // レコードなし → 新規登録
        if (!$favorite) {
            UsrFavoriteCar::create([
                'user_id' => $userId,
                'car_id'  => $carId,
            ]);
            return true;
        }

        // 解除済み → 復活
        if ($favorite->trashed()) {
            $favorite->restore();
            return true;
        }

        // 登録済み → 解除
        $favorite->delete();
        return false;
    }

    /**
     * お気に入り状態確認
     */
    public function isFavorite(string $userId, int $carId): bool
    {
        return UsrFavoriteCar::where('user_id', $userId)
            ->where('car_id', $carId)
            ->exists();
    }

    /**
     * ユーザーのお気に入り一覧取得
     */
    public function findByUserId(string $userId): Collection
    {
        return UsrFavoriteCar::where('user_id', $userId)
            ->with('car')
            ->get();
    }
}