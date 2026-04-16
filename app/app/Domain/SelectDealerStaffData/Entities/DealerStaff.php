<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerStaffData\Entities;

final class DealerStaff
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $dealerId,
        public readonly ?int    $userId,
        public readonly string  $name,
        public readonly ?string $position,
        public readonly ?string $imageUrl,
        public readonly ?string $comment,
        public readonly int     $sortOrder,
    ) {}

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'dealerId'  => $this->dealerId,
            'userId'    => $this->userId,
            'name'      => $this->name,
            'position'  => $this->position,
            'imageUrl'  => $this->imageUrl,
            'comment'   => $this->comment,
            'sortOrder' => $this->sortOrder,
        ];
    }
}