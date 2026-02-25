<?php

namespace App\Application\UseCases\MileageList;

use App\Domain\MileageList\Repositories\MileageRepositoryInterface;
use Exception;

class MileageListUseCase
{
    private MileageRepositoryInterface $mileageRepository;

    public function __construct(MileageRepositoryInterface $mileageRepository)
    {
        $this->mileageRepository = $mileageRepository;
    }

    /**
     * 距離一覧を取得する
     * 
     * @return MileageListOutputData
     * @throws Exception
     */
    public function execute(): MileageListOutputData
    {
        try {
            $mileages = $this->mileageRepository->findActive();
            return new MileageListOutputData($mileages);
        } catch (Exception $e) {
            throw new Exception('価格一覧の取得に失敗しました: ' . $e->getMessage());
        }
    }
}