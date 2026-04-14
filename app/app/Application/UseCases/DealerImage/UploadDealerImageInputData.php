<?php

declare(strict_types=1);

namespace App\Application\UseCases\DealerImage;

use App\Domain\DealerImage\ValueObjects\DealerId;
use Illuminate\Http\UploadedFile;

final class UploadDealerImageInputData
{
    public function __construct(
        public readonly DealerId     $dealerId,
        public readonly UploadedFile $file,
        public readonly ?string      $altText = null,
    ) {}
}