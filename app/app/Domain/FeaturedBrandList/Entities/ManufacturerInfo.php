<?php
namespace App\Domain\FeaturedBrandList\Entities;

class ManufacturerInfo 
{
    private FeaturedBrand $featuredBrand;
    private ?Manufacturer $manufacturer;
    private ?ManufacturerImage $manufacturerImage;

    public function __construct(
        FeaturedBrand $featuredBrand, 
        ?Manufacturer $manufacturer = null,
        ?ManufacturerImage $manufacturerImage = null
    ) {
        $this->featuredBrand = $featuredBrand;
        $this->manufacturer = $manufacturer;
        $this->manufacturerImage = $manufacturerImage;
    }

    // ゲッター
    public function getId(): int { return $this->featuredBrand->getId(); }
    public function getPosition(): ?string { return $this->featuredBrand->getPosition(); }
    public function getSortOrder(): int { return $this->featuredBrand->getSortOrder(); }
    public function getIsActive(): bool { return $this->featuredBrand->getIsActive(); }
    
    public function getName(): ?string { return $this->manufacturer?->getName(); }
    public function getNameKana(): ?string { return $this->manufacturer?->getNameKana(); }
    public function getDisplayName(): ?string { return $this->manufacturer?->getDisplayName(); }
    public function getCode(): string { return $this->featuredBrand->getManufacturerCode(); }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'position' => $this->getPosition(),
            'sortOrder' => $this->getSortOrder(),
            'isActive' => $this->getIsActive(),
            'name' => $this->getName() ?? '',
            'nameKana' => $this->getNameKana() ?? '',
            'displayName' => $this->getDisplayName() ?? '',
            'code' => $this->getCode(),
            'url' => $this->manufacturer?->getUrl() ?? '',
            'description' => $this->manufacturer?->getDescription() ?? '',
            'countryCode' => $this->manufacturer?->getCountryCode() ?? '',
        ];
    }
}
