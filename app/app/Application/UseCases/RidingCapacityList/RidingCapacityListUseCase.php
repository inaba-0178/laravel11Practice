<?php

namespace App\Application\UseCases\RidingCapacityList;

use App\Domain\RidingCapacityList\Repositories\RidingCapacityRepositoryInterface;
use Exception;

class RidingCapacityListUseCase
{
    private RidingCapacityRepositoryInterface $ridingCapacityRepository;

    public function __construct(RidingCapacityRepositoryInterface $ridingCapacityRepository)
    {
        $this->RidingCapacityRepository = $ridingCapacityRepository;
    }

    /**
     * 乗車定員一覧を取得する
     * 
     * @return RidingCapacityListOutputData
     * @throws Exception
     */
    public function execute(): RidingCapacityListOutputData
    {
        try {
            $ridingCapacities = $this->RidingCapacityRepository->findActive();
            \Log::info($ridingCapacities);
            return new RidingCapacityListOutputData($ridingCapacities);
        } catch (Exception $e) {
            throw new Exception('排気量一覧の取得に失敗しました: ' . $e->getMessage());
        }
    }
}