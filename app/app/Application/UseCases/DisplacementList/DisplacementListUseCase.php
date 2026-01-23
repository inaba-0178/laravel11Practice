<?php

namespace App\Application\UseCases\DisplacementList;

use App\Domain\DisplacementList\Repositories\DisplacementRepositoryInterface;
use Exception;

class DisplacementListUseCase
{
    private DisplacementRepositoryInterface $displacementRepository;

    public function __construct(DisplacementRepositoryInterface $displacementRepository)
    {
        $this->DisplacementRepository = $displacementRepository;
    }

    /**
     * 排気量一覧を取得する
     * 
     * @return DisplacementListOutputData
     * @throws Exception
     */
    public function execute(): DisplacementListOutputData
    {
        try {
            $displacements = $this->DisplacementRepository->findActive();
            \Log::info($displacements);
            return new DisplacementListOutputData($displacements);
        } catch (Exception $e) {
            throw new Exception('排気量一覧の取得に失敗しました: ' . $e->getMessage());
        }
    }
}