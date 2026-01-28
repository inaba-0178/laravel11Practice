<?php

namespace App\Application\UseCases\FeaturedBrandList;

class FeaturedBrandListMapper
{
    public function mergeManufacturerInfo(
        array $featuredBrands, 
        array $manufacturers,
        array $manufacturerImages,
    ): array {
        $manufacturerMap = collect($manufacturers)
            ->keyBy(fn($manufacturer) => $manufacturer->getCode())
            ->toArray();

        $manufacturerImageMap = collect($manufacturerImages)
            ->keyBy(fn($manufacturerImage) => $manufacturerImage->getManufacturerId())
            ->toArray();

        return array_map(function ($featured) use ($manufacturerMap, $manufacturerImageMap) {
            $manufacturer = $manufacturerMap[$featured->getManufacturerCode()] ?? null;
            $manufacturerImage = $manufacturerImageMap[$featured->getId()] ?? null;
            
            return [
                'id' => $featured->getId(),
                'position' => $featured->getPosition(),
                'sortOrder' => $featured->getSortOrder(),
                'isActive' => $featured->getIsActive(),
                // メーカー情報マージ
                'name' => $manufacturer?->getName() ?? '',
                'nameKana' => $manufacturer?->getNameKana() ?? '',
                'displayName' => $manufacturer?->getDisplayName() ?? '',
                'code' => $featured->getManufacturerCode(),
                'url' => $manufacturer?->getUrl() ?? '',
                'description' => $manufacturer?->getDescription() ?? '',
                'countryCode' => $manufacturer?->getCountryCode() ?? '',
                'manufacturerIsActive' => $manufacturer?->getIsActive() ?? false,
                // メーカー画像情報マージ
                'manufacturerImageType' => $manufacturerImage?->getImageType() ?? '',
                'manufacturerImageFilePath' => $manufacturerImage?->getFilePath() ?? '',
                'manufacturerImageAltText' => $manufacturerImage?->getAltText() ?? '',
            ];
        }, $featuredBrands);
    }
}