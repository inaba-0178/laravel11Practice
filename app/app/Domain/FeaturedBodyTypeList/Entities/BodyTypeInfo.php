<?php
namespace App\Domain\FeaturedBodyTypeList\Entities;

class BodyTypeInfo 
{
    private FeaturedBodyType $featuredBodyType;
    private ?BodyType $bodyType;
    private ?BodyTypeImage $bodyTypeImage;

    public function __construct(
        FeaturedBodyType $featuredBodyType, 
        ?BodyType $bodyType = null,
        ?BodyTypeImage $bodyTypeImage = null
    ) {
        $this->featuredBodyType = $featuredBodyType;
        $this->bodyType = $bodyType;
        $this->bodyTypeImage = $bodyTypeImage;
    }

    public function getId(): int { return $this->featuredBodyType->getId(); }
    public function getPosition(): ?string { return $this->featuredBodyType->getPosition(); }
    public function getSortOrder(): int { return $this->featuredBodyType->getSortOrder(); }
    public function getIsActive(): int { return $this->featuredBodyType->getIsActive(); }
    
    public function getName(): ?string { return $this->bodyType?->getName(); }
    public function getNameKana(): ?string { return $this->bodyType?->getNameKana(); }
    public function getCode(): string { return $this->featuredBodyType->getBodyTypeCode(); }

    public function getBodyTypeImageType(): ?string { 
        return $this->bodyTypeImage?->getImageType(); 
    }
    public function getBodyTypeImageFilePath(): ?string { 
        return $this->bodyTypeImage?->getFilePath(); 
    }
    public function getBodyTypeImageAltText(): ?string { 
        return $this->bodyTypeImage?->getAltText(); 
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'position' => $this->getPosition(),
            'sortOrder' => $this->getSortOrder(),
            'isActive' => $this->getIsActive(),
            'name' => $this->getName() ?? '',
            'nameKana' => $this->getNameKana() ?? '',
            'code' => $this->getCode(),
            'description' => $this->bodyType?->getDescription() ?? '',
            'availableCountries' => $this->bodyType?->getAvailableCountries() ?? '',
            'bodyTypeIsActive' => $this->bodyType?->getIsActive() ?? false,
            'bodyTypeImageType' => $this->getBodyTypeImageType() ?? '',
            'bodyTypeImageFilePath' => $this->getBodyTypeImageFilePath() ?? '',
            'bodyTypeImageAltText' => $this->getBodyTypeImageAltText() ?? '',
        ];
    }
}
