<?php

namespace App\Domain\FeaturedBodyTypeList\Services;

use App\Domain\FeaturedBodyTypeList\Entities\FeaturedBodyType;
use App\Domain\FeaturedBodyTypeList\Entities\BodyType;
use App\Domain\FeaturedBodyTypeList\Entities\BodyTypeImage;
use App\Domain\FeaturedBodyTypeList\Entities\BodyTypeInfo;

class BodyTypeInfoFactory
{
    /**
     * BodyType配列からBodyTypeInfo配列を生成
     * 
     * @param FeaturedBodyType[] $featuredBodyType
     * @param BodyType[] $bodyTypes
     * @param BodyTypeImage[] $bodyTypeImages
     * @return BodyTypeInfo[]
     */
    public function createFromFeaturedBodyTypes(
        array $featuredBodyType,
        array $bodyTypes,
        array $bodyTypeImages
    ): array {
        $bodyTypeMap = $this->createBodyTypeMap($bodyTypes);
        $imageMap = $this->createImageMap($bodyTypeImages);

        return array_map(
            fn($featured) => $this->createBodyTypeInfo(
                $featured,
                $bodyTypeMap,
                $imageMap
            ),
            $featuredBodyType
        );
    }

    private function createBodyTypeInfo(
        FeaturedBodyType $featuredBodyType,
        array $bodyTypeMap,
        array $imageMap
    ): BodyTypeInfo {
        $bodyType = $bodyTypeMap[$featuredBodyType->getBodyTypeCode()] ?? null;
        $image = $bodyType ? ($imageMap[$bodyType->getId()] ?? null) : null;

        return new BodyTypeInfo(
            $featuredBodyType,
            $bodyType,
            $image
        );
    }

    private function createBodyTypeMap(array $bodyTypes): array
    {
        $map = [];
        foreach ($bodyTypes as $bodyType) {
            $map[$bodyType->getCode()] = $bodyType;
        }
        return $map;
    }

    private function createImageMap(array $images): array
    {
        $map = [];
        foreach ($images as $image) {
            $map[$image->getBodyTypeId()] = $image;
        }
        return $map;
    }
}