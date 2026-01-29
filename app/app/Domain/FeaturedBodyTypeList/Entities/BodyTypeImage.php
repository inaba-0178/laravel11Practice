<?php
namespace App\Domain\FeaturedBodyTypeList\Entities;

class BodyTypeImage
{
    private int $id;
    private int $bodyTypeId;
    private ?string $imageType;
    private ?string $filePath;
    private ?string $altText;
    private int $sortOrder;
    private int $isMain;
    private int $isActive;

    public function __construct(
        int $id,
        int $bodyTypeId,
        ?string $imageType,
        ?string $filePath,
        ?string $altText,
        int $sortOrder,
        int $isMain,
        int $isActive,
    ) {
        $this->id = $id;
        $this->bodyTypeId = $bodyTypeId;
        $this->imageType = $imageType;
        $this->filePath = $filePath;
        $this->altText = $altText;
        $this->sortOrder = $sortOrder;
        $this->isMain = $isMain;
        $this->isActive = $isActive;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBodyTypeId(): int
    {
        return $this->bodyTypeId;
    }

    public function getImageType(): ?string
    {
        return $this->imageType;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function getIsMain(): int
    {
        return $this->isMain;
    }

    public function getIsActive(): int
    {
        return $this->isActive;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'bodyTypeId' => $this->bodyTypeId,
            'imageType' => $this->imageType ?? '',
            'filePath' => $this->filePath ?? '',
            'altText' => $this->altText ?? '',
            'sortOrder' => $this->sortOrder,
            'isMain' => $this->isMain,
            'isActive' => $this->isActive,
        ];
    }

}
