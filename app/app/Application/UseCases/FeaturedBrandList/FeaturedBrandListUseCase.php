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

            $mapper = new FeaturedBrandListMapper();
            $manufacturerInfoList = $mapper->mergeManufacturerInfo($featuredBrands, $manufacturers, $manufacturerImages);

            return new FeaturedBrandListOutputData($manufacturerInfoList);
        } catch (Exception $e) {
            throw new Exception('topページメーカー一覧表示データの取得に失敗しました: ' . $e->getMessage());
        }
    }
}