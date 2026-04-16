<?php

declare(strict_types=1);

namespace App\Domain\Asset\Entities;

final class Asset
{
    public function __construct(
        public readonly int    $id,
        public readonly string $type,
        public readonly string $imagePath,
    ) {}

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'type'      => $this->type,
            'imagePath' => $this->imagePath,
        ];
    }
}