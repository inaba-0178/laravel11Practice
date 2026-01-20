<?php

namespace App\Application\UseCases\GetRegionList;

use App\Domain\GetRegionList\Repositories\RegionRepositoryInterface;
use Exception;

class GetRegionListUseCase
{
    private RegionRepositoryInterface $regionRepository;

    public function __construct(RegionRepositoryInterface $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }

    /**
     * Region一覧を取得する
     * 
     * @param GetRegionListInputData $inputData
     * @return GetRegionListOutputData
     * @throws Exception
     */
    public function execute(GetRegionListInputData $inputData): GetRegionListOutputData
    {
        try {
            if ($inputData->isActiveOnly()) {
                $regions = $this->regionRepository->findActive();
            } else {
                $regions = $this->regionRepository->findAll();
            }

            return new GetRegionListOutputData($regions);
        } catch (Exception $e) {
            throw new Exception('Region一覧の取得に失敗しました: ' . $e->getMessage());
        }
    }
}