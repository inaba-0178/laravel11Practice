<?php

namespace App\Application\UseCases\FeaturedBrandList;

use App\Domain\FeaturedBrandList\Repositories\FeaturedBrandRepositoryInterface;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerRepositoryInterface;
use App\Domain\FeaturedBrandList\Repositories\ManufacturerImageRepositoryInterface;
use App\Domain\FeaturedBrandList\Services\ManufacturerInfoFactory;
use Exception;

class FeaturedBrandListUseCase
{
    private FeaturedBrandRepositoryInterface $featuredBrandRepository;
    private ManufacturerRepositoryInterface $manufacturerRepository;
    private ManufacturerImageRepositoryInterface $manufacturerImageRepository;
    private ManufacturerInfoFactory $factory;

    public function __construct(
        FeaturedBrandRepositoryInterface $featuredBrandRepository,
        ManufacturerRepositoryInterface $manufacturerRepository,
        ManufacturerImageRepositoryInterface $manufacturerImageRepository,
        ManufacturerInfoFactory $factory
    )
    {
        $this->featuredBrandRepository = $featuredBrandRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->manufacturerImageRepository = $manufacturerImageRepository;
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
            $featuredBrands = $this->featuredBrandRepository->findActive();
            
            $manufacturerCodes = array_map(fn($featuredBrand) => $featuredBrand->getManufacturerCode(), $featuredBrands);
            $manufacturers = $this->manufacturerRepository->findByCodes($manufacturerCodes);

            $manufacturerIds = array_map(fn($m) => $m->getId(), $manufacturers);
            $manufacturerImages = $this->manufacturerImageRepository->findByManufacturerIds($manufacturerIds);

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