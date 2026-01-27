<?php

namespace App\Application\UseCases\FeaturedBrandList;

use App\Domain\FeaturedBrandList\Repositories\FeaturedBrandRepositoryInterface;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerRepositoryInterface;
use Exception;

class FeaturedBrandListUseCase
{
    private FeaturedBrandRepositoryInterface $FeaturedBrandListRepository;
    private ManufacturerRepositoryInterface $ManufacturerRepositoryInterface;

    public function __construct(
        FeaturedBrandRepositoryInterface $FeaturedBrandRepository,
        ManufacturerRepositoryInterface $ManufacturerRepository,
    )
    {
        $this->FeaturedBrandRepository = $FeaturedBrandRepository;
        $this->ManufacturerRepository = $ManufacturerRepository;
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
            $manufacturers = $this->ManufacturerRepository->findByCodes($manufacturerCodes);
            $manufacturerInfoList = $this->mergeFeaturedWithManufacturers($featuredBrands, $manufacturers);

            \Log::info($manufacturerInfoList);
            return new FeaturedBrandListOutputData($manufacturerInfoList);
        } catch (Exception $e) {
            throw new Exception('topページメーカー一覧表示データの取得に失敗しました: ' . $e->getMessage());
        }
    }

    private function mergeFeaturedWithManufacturers(
        array $featuredBrands, 
        array $manufacturers
    ): array {
        $manufacturerMap = collect($manufacturers)
            ->keyBy(fn($m) => $m->getCode())
            ->toArray();
            
        return array_map(function ($featured) use ($manufacturerMap) {
            $manufacturer = $manufacturerMap[$featured->getManufacturerCode()] ?? null;
            
            return [
                'id' => $featured->getId(),
                'position' => $featured->getPosition(),
                'sortOrder' => $featured->getSortOrder(),
                'isActive' => $featured->getIsActive(),
                // マージ！
                'name' => $manufacturer?->getName() ?? '',
                'nameKana' => $manufacturer?->getNameKana() ?? '',
                'displayName' => $manufacturer?->getDisplayName() ?? '',
                'code' => $featured->getManufacturerCode(),
                'url' => $manufacturer?->getUrl() ?? '',
                'description' => $manufacturer?->getDescription() ?? '',
                'countryCode' => $manufacturer?->getCountryCode() ?? '',
                'manufacturerIsActive' => $manufacturer?->getIsActive() ?? false,
            ];
        }, $featuredBrands);
    }
}