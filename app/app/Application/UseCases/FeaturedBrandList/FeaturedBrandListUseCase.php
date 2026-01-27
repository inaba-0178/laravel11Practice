<?php

namespace App\Application\UseCases\FeaturedBrandList;

use App\Domain\FeaturedBrandList\Repositories\FeaturedBrandRepositoryInterface;
use Exception;

class FeaturedBrandListUseCase
{
    private FeaturedBrandRepositoryInterface $FeaturedBrandListRepository;

    public function __construct(FeaturedBrandRepositoryInterface $FeaturedBrandRepository)
    {
        $this->FeaturedBrandRepository = $FeaturedBrandRepository;
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
            $FeaturedBrands = $this->FeaturedBrandRepository->findActive();
            \Log::info($FeaturedBrands);
            return new FeaturedBrandListOutputData($FeaturedBrands);
        } catch (Exception $e) {
            throw new Exception('topページメーカー一覧表示データの取得に失敗しました: ' . $e->getMessage());
        }
    }
}