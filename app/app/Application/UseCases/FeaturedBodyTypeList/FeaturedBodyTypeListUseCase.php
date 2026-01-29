<?php

namespace App\Application\UseCases\FeaturedBodyTypeList;

use App\Domain\FeaturedBodyTypeList\Repositories\FeaturedBodyTypeRepositoryInterface;
use App\Domain\FeaturedBodyTypeList\Repositories\BodyTypeRepositoryInterface;
use App\Domain\FeaturedBodyTypeList\Repositories\BodyTypeImageRepositoryInterface;
use App\Domain\FeaturedBodyTypeList\Services\BodyTypeInfoFactory;
use Exception;

class FeaturedBodyTypeListUseCase
{
    private FeaturedBodyTypeRepositoryInterface $featuredBodyTypeRepository;
    private BodyTypeRepositoryInterface $bodyTypeRepository;
    private BodyTypeImageRepositoryInterface $bodyTypeImageRepository;
    private BodyTypeInfoFactory $factory;

    public function __construct(
        FeaturedBodyTypeRepositoryInterface $featuredBodyTypeRepository,
        BodyTypeRepositoryInterface $bodyTypeRepository,
        BodyTypeImageRepositoryInterface $bodyTypeImageRepository,
        BodyTypeInfoFactory $factory
    )
    {
        $this->featuredBodyTypeRepository = $featuredBodyTypeRepository;
        $this->bodyTypeRepository = $bodyTypeRepository;
        $this->bodyTypeImageRepository = $bodyTypeImageRepository;
        $this->factory = $factory;
    }

    /**
     * topページメーカー一覧表示データ取得する
     * 
     * @return FeaturedBodyTypeListOutputData
     * @throws Exception
     */
    public function execute(): FeaturedBodyTypeListOutputData
    {
        try {

            $featuredBodyTypes = $this->featuredBodyTypeRepository->findActive();

            $bodyTypeCodes = array_map(fn($featuredBodyType) => $featuredBodyType->getBodyTypeCode(), $featuredBodyTypes);
            $bodyTypes = $this->bodyTypeRepository->findByCodes($bodyTypeCodes);

            $bodyTypeIds = array_map(fn($m) => $m->getId(), $bodyTypes);
            $bodyTypeImages = $this->bodyTypeImageRepository->findByBodyTypeIds($bodyTypeIds);

            $bodyTypeInfoList = $this->factory->createFromFeaturedBodyTypes(
                $featuredBodyTypes,
                $bodyTypes,
                $bodyTypeImages
            );

            return new FeaturedBodyTypeListOutputData($bodyTypeInfoList);
        } catch (Exception $e) {
            throw new Exception('topページボディタイプ一覧表示データの取得に失敗しました: ' . $e->getMessage());
        }
    }
}