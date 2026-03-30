<?php

declare(strict_types=1);

namespace App\Domain\CarImage\Entities;

final class CarImage
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $carId,
        public readonly string  $imageUrl,
        public readonly string  $imageType,
        public readonly int     $displayOrder,
        public readonly int     $isMain,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getCarId(): int
    {
        return $this->carId;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getImageType(): string
    {
        return $this->imageType;
    }

    public function getDisplayOrder(): int
    {
        return $this->displayOrder;
    }

    public function getIsMain(): int
    {
        return $this->isMain;
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'carId'        => $this->carId,
            'imageUrl'     => $this->imageUrl,
            'imageType'    => $this->imageType,
            'displayOrder' => $this->displayOrder,
            'isMain'       => $this->isMain,
        ];
    }
}