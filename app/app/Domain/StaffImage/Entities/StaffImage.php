<?php

declare(strict_types=1);

namespace App\Domain\StaffImage\Entities;

final class StaffImage
{
    public function __construct(
        public readonly int    $id,
        public readonly int    $dealerId,
        public readonly ?int   $userId,
        public readonly string $imagePath,
    ) {}

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'dealerId'  => $this->dealerId,
            'userId'    => $this->userId,
            'imagePath' => $this->imagePath,
        ];
    }
}