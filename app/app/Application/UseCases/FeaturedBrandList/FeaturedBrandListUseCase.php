<?php

namespace App\Application\UseCases\FeaturedBrandList;

use App\Domain\FeaturedBrandList\Repositories\FeaturedBrandRepositoryInterface;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerRepositoryInterface;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerImageRepositoryInterface;
use App\Domain\FeaturedBrandList\Services\ManufacturerInfoFactory;
use Exception;

class FeaturedBrandListUseCase
{
    private FeaturedBrandRepositoryInterface $FeaturedBrandRepository;
    private ManufacturerRepositoryInterface $ManufacturerRepository;
    private ManufacturerImageRepositoryInterface $ManufacturerImageRepository;
    private ManufacturerInfoFactory $factory;

    public function __construct(
        FeaturedBrandRepositoryInterface $FeaturedBrandRepository,
        ManufacturerRepositoryInterface $ManufacturerRepository,
        ManufacturerImageRepositoryInterface $ManufacturerImageRepository,
        ManufacturerInfoFactory $factory
    )
    {
        $this->FeaturedBrandRepository = $FeaturedBrandRepository;
        $this->ManufacturerRepository = $ManufacturerRepository;
        $this->ManufacturerImageRepository = $ManufacturerImageRepository;
        $this->factory = $factory;
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
            $manufacturerIds = array_map(fn($m) => $m->getId(), $manufacturers);
            $manufacturerImages = $this->ManufacturerImageRepository->findByManufacturerIds($manufacturerIds);

            $manufacturerInfoList = $this->factory->createFromFeaturedBrands(
                $featuredBrands,
                $manufacturers,
                $manufacturerImages
            );

            return new FeaturedBrandListOutputData($manufacturerInfoList);
        } catch (Exception $e) {
            throw new Exception('topページメーカー一覧表示データの取得に失敗しました: ' . $e->getMessage());
        }
    }
}