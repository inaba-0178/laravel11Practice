<?php
namespace App\Domain\FeaturedBodyTypeList\Repositories;

interface BodyTypeImageRepositoryInterface
{
    /**
     * すべてのBodyTypeImageを取得
     * 
     * @return BodyTypeImage[]
     */
    public function findAll(): array;

    /**
     * 指定されたボディタイプIDに紐づく画像を取得
     * 
     * @param int[] $bodyTypeIds
     * @param array $conditions
     * @return BodyTypeImage[]
     */
    public function findByBodyTypeIds(array $bodyTypeIds, array $conditions = []): array;

}