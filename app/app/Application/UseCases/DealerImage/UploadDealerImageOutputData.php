<?php

declare(strict_types=1);

namespace App\Application\UseCases\DealerImage;

use App\Domain\DealerImage\Entities\DealerImage;

final class UploadDealerImageOutputData
{
    public function __construct(
        private readonly DealerImage $dealerImage,
    ) {}

    public function toArray(): array
    {
        return [
            'success'     => true,
            'dealerImage' => $this->dealerImage->toArray(),
        ];
    }
}