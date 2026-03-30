<?php

declare(strict_types=1);

namespace App\Application\UseCases\CarImage;

use App\Domain\CarImage\Repositories\CarImageUploadRepositoryInterface;
use Illuminate\Support\Facades\Storage;

final class UploadCarImageUseCase
{
    public function __construct(
        private readonly CarImageUploadRepositoryInterface $repository,
    ) {}

    public function execute(UploadCarImageInputData $input): UploadCarImageOutputData
    {
        $carId     = $input->carId->getValue();
        $imageType = $input->imageType->getValue();

        // S3に保存
        // 将来的に動画対応する場合はdisk名やディレクトリを分岐させる
        $path = $input->file->store("{$carId}", 's3');

        // display_order を採番
        $maxOrder     = $this->repository->getMaxDisplayOrder($carId);
        $displayOrder = $maxOrder + 1;

        // DBに保存
        $carImage = $this->repository->save(
            carId        : $carId,
            storedPath   : $path,
            imageType    : $imageType,
            displayOrder : $displayOrder,
        );

        return new UploadCarImageOutputData($carImage);
    }
}