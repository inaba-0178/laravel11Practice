<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerContentData\Entities;

final class DealerContent
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $dealerId,
        public readonly string  $category,
        public readonly string  $title,
        public readonly ?string $description,
        public readonly ?string $imageUrl,
        public readonly int     $sortOrder,
        public readonly ?string $startedAt,
        public readonly ?string $endedAt,
    ) {}

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'dealerId'    => $this->dealerId,
            'category'    => $this->category,
            'title'       => $this->title,
            'description' => $this->description,
            'imageUrl'    => $this->imageUrl,
            'sortOrder'   => $this->sortOrder,
            'startedAt'   => $this->startedAt,
            'endedAt'     => $this->endedAt,
        ];
    }
}