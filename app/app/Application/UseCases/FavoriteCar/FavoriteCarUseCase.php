<?php

namespace App\Application\UseCases\FavoriteCar;

use App\Domain\FavoriteCar\Repositories\FavoriteCarRepositoryInterface;
use App\Domain\FavoriteCar\ValueObjects\UserId;
use App\Domain\FavoriteCar\ValueObjects\CarId;

class FavoriteCarUseCase
{
    public function __construct(
        private readonly FavoriteCarRepositoryInterface $repository,
    ) {}

    /**
     * お気に入り登録・解除トグル
     */
    public function toggle(UserId $userId, CarId $carId): FavoriteCarOutputData
    {
        $isFavorite = $this->repository->toggle($userId->getValue(), $carId->getValue());

        return new FavoriteCarOutputData($isFavorite);
    }

    /**
     * お気に入り状態確認
     */
    public function isFavorite(UserId $userId, CarId $carId): FavoriteCarOutputData
    {
        $isFavorite = $this->repository->isFavorite($userId->getValue(), $carId->getValue());

        return new FavoriteCarOutputData($isFavorite);
    }

    /**
     * お気に入り一覧取得
     */
    public function list(UserId $userId): FavoriteCarListOutputData
    {
        $favorites = $this->repository->findByUserId($userId->getValue());

        return new FavoriteCarListOutputData($favorites);
    }
}