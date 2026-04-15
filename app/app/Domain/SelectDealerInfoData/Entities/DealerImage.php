<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerInfoData\Entities;

final class DealerImage
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $dealerId,
        public readonly string  $imageUrl,
        public readonly ?string $altText,
        public readonly bool    $isMain,
        public readonly int     $sortOrder,
        public readonly ?string $caption,
    ) {}

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'dealerId'  => $this->dealerId,
            'imageUrl'  => $this->imageUrl,
            'altText'   => $this->altText,
            'isMain'    => $this->isMain,
            'sortOrder' => $this->sortOrder,
            'caption'   => $this->caption,
        ];
    }
}