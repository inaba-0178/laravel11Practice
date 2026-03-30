<?php

declare(strict_types=1);

namespace App\Domain\CarImage\Repositories;

use App\Domain\CarImage\Entities\CarImage;

interface CarImageUploadRepositoryInterface
{
    /**
     * 画像を保存してエンティティを返す
     *
     * @param int    $carId
     * @param string $storedPath  S3に保存済みのパス
     * @param string $imageType
     * @param int    $displayOrder
     * @return CarImage
     */
    public function save(
        int    $carId,
        string $storedPath,
        string $imageType,
        int    $displayOrder,
    ): CarImage;

    /**
     * 対象車両の最大display_orderを取得
     *
     * @param int $carId
     * @return int
     */
    public function getMaxDisplayOrder(int $carId): int;
}