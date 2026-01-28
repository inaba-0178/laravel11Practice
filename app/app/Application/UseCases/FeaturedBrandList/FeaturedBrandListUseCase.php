<?php

namespace App\Application\UseCases\FeaturedBrandList;

use App\Domain\FeaturedBrandList\Repositories\FeaturedBrandRepositoryInterface;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerRepositoryInterface;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerImageRepositoryInterface;
use Exception;

class FeaturedBrandListUseCase
{
    private FeaturedBrandRepositoryInterface $FeaturedBrandListRepository;
    private ManufacturerRepositoryInterface $ManufacturerRepositoryInterface;
    private ManufacturerImageRepositoryInterface $ManufacturerImageRepositoryInterface;

    public function __construct(
        FeaturedBrandRepositoryInterface $FeaturedBrandRepository,
        ManufacturerRepositoryInterface $ManufacturerRepository,
        ManufacturerImageRepositoryInterface $ManufacturerImageRepository,
    )
    {
        $this->FeaturedBrandRepository = $FeaturedBrandRepository;
        $this->ManufacturerRepository = $ManufacturerRepository;
        $this->ManufacturerImageRepository = $ManufacturerImageRepository;
    }

    /**
     * topページメーカー一覧表示データ取得する
     * 
     * @return FeaturedBrandListOutputData
     * @throws Exception
     */
    public function execute(): FeaturedBrandListOutputData
    {
        try {
            $featuredBrands = $this->FeaturedBrandRepository->findActive();
            $manufacturerCodes = array_map(fn($featuredBrand) => $featuredBrand->getManufacturerCode(), $featuredBrands);
            $manufacturerIds = array_map(fn($featuredBrand) => $featuredBrand->getId(), $featuredBrands);
            
            $manufacturers = $this->ManufacturerRepository->findByCodes($manufacturerCodes);
            $manufacturerImages = $this->ManufacturerImageRepository->findByManufacturerIds($manufacturerIds);

            $manufacturerInfoList = $this->mergeFeaturedWithManufacturers($featuredBrands, $manufacturers, $manufacturerImages);

            \Log::info($manufacturerInfoList);
            
            \Log::info($manufacturerImages);

            return new FeaturedBrandListOutputData($manufacturerInfoList);
        } catch (Exception $e) {
            throw new Exception('topページメーカー一覧表示データの取得に失敗しました: ' . $e->getMessage());
        }
    }

    private function mergeFeaturedWithManufacturers(
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