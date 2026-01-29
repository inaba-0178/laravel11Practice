<?php

namespace App\Domain\FeaturedBrandList\Services;

use App\Domain\FeaturedBrandList\Entities\FeaturedBrand;
use App\Domain\FeaturedBrandList\Entities\Manufacturer;
use App\Domain\FeaturedBrandList\Entities\ManufacturerImage;
use App\Domain\FeaturedBrandList\Entities\ManufacturerInfo;

class ManufacturerInfoFactory
{
    /**
     * FeaturedBrand配列からManufacturerInfo配列を生成
     * 
     * @param FeaturedBrand[] $featuredBrands
     * @param Manufacturer[] $manufacturers
     * @param ManufacturerImage[] $manufacturerImages
     * @return ManufacturerInfo[]
     */
    public function createFromFeaturedBrands(
        array $featuredBrands,
        array $manufacturers,
        array $manufacturerImages
    ): array {
        $manufacturerMap = $this->createManufacturerMap($manufacturers);
        $imageMap = $this->createImageMap($manufacturerImages);

        return array_map(
            fn($featured) => $this->createManufacturerInfo(
                $featured,
                $manufacturerMap,
                $imageMap
            ),
            $featuredBrands
        );
    }

    private function createManufacturerInfo(
        FeaturedBrand $featuredBrand,
        array $manufacturerMap,
        array $imageMap
    ): ManufacturerInfo {
        $manufacturer = $manufacturerMap[$featuredBrand->getManufacturerCode()] ?? null;
        $image = $manufacturer ? ($imageMap[$manufacturer->getId()] ?? null) : null;

        return new ManufacturerInfo(
            $featuredBrand,
            $manufacturer,
            $image
        );
    }

    private function createManufacturerMap(array $manufacturers): array
    {
        $map = [];
        foreach ($manufacturers as $manufacturer) {
            $map[$manufacturer->getCode()] = $manufacturer;
        }
        return $map;
    }

    private function createImageMap(array $images): array
    {
        $map = [];
        foreach ($images as $image) {
            $map[$image->getManufacturerId()] = $image;
        }
        return $map;
    }
}