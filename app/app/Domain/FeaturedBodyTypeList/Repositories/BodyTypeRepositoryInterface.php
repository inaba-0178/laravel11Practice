<?php
namespace App\Domain\FeaturedBodyTypeList\Repositories;

interface BodyTypeRepositoryInterface
{
    /**
     * 指定されたボディタイプIDに紐づく画像を取得
     * 
     * @param array $codes
     * @param array $conditions
     * @return BodyTypeImage[]
     */
    public function findByCodes(array $codes, array $conditions = []): array;
}