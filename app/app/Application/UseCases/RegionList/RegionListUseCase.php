<?php

namespace App\Application\UseCases\RegionList;

use App\Domain\RegionList\Repositories\RegionRepositoryInterface;
use Exception;

class RegionListUseCase
{
    private RegionRepositoryInterface $regionRepository;

    public function __construct(RegionRepositoryInterface $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }

    /**
     * 都道府県一覧を取得する
     * 
     * @return RegionListOutputData
     * @throws Exception
     */
    public function execute(): RegionListOutputData
    {
        try {
            $regions = $this->regionRepository->findActive();
            return new RegionListOutputData($regions);
        } catch (Exception $e) {
            throw new Exception('都道府県一覧の取得に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * 地方ごとに都道府県一覧を取得する
     * 
     * @return GetAreaListOutputData
     * @throws Exception
     */
    public function executeGroupedByArea(): GetAreaListOutputData
    {
        try {
            $areas = $this->regionRepository->findAllGroupedByArea();
            return new GetAreaListOutputData($areas);
        } catch (Exception $e) {
            throw new Exception('地方別Region一覧の取得に失敗しました: ' . $e->getMessage());
        }
    }
}