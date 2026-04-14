<?php

declare(strict_types=1);

namespace App\Application\UseCases\DealerImage;

use App\Domain\DealerImage\Repositories\DealerImageUploadRepositoryInterface;

final class UploadDealerImageUseCase
{
    private const MAX_IMAGES = 10;

    public function __construct(
        private readonly DealerImageUploadRepositoryInterface $repository,
    ) {}

    public function execute(UploadDealerImageInputData $input): UploadDealerImageOutputData
    {
        $dealerId = $input->dealerId->getValue();

        // 枚数チェック
        $currentCount = $this->repository->countByDealerId($dealerId);
        if ($currentCount >= self::MAX_IMAGES) {
            throw new \RuntimeException('画像は最大' . self::MAX_IMAGES . '枚まで登録できます。');
        }

        // S3に保存
        $path = $input->file->store("dealers/{$dealerId}", 's3');

        // sort_orderを採番
        $maxOrder  = $this->repository->getMaxSortOrder($dealerId);
        $sortOrder = $maxOrder + 1;

        // DBに保存
        $dealerImage = $this->repository->save(
            dealerId   : $dealerId,
            storedPath : $path,
            altText    : $input->altText,
            sortOrder  : $sortOrder,
        );

        return new UploadDealerImageOutputData($dealerImage);
    }
}