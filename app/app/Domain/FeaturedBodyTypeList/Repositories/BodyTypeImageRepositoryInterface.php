<?php
namespace App\Domain\FeaturedBodyTypeList\Repositories;

interface BodyTypeImageRepositoryInterface
{

    /**
     * 指定されたボディタイプIDに紐づく画像を取得
     * 
     * @param int[] $bodyTypeIds
     * @param array $conditions
     * @return BodyTypeImage[]
     */
    public function findByBodyTypeIds(array $bodyTypeIds, array $conditions = []): array;

}