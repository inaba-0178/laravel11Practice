<?php

namespace App\Application\UseCases\PriceList;

use App\Domain\PriceList\Repositories\PriceRepositoryInterface;
use Exception;

class PriceListUseCase
{
    private PriceRepositoryInterface $PriceRepository;

    public function __construct(PriceRepositoryInterface $PriceRepository)
    {
        $this->PriceRepository = $PriceRepository;
    }

    /**
     * 価格一覧を取得する
     * 
     * @return PriceListOutputData
     * @throws Exception
     */
    public function execute(): PriceListOutputData
    {
        try {
            $Prices = $this->PriceRepository->findActive();
            \Log::info($Prices);
            return new PriceListOutputData($Prices);
        } catch (Exception $e) {
            throw new Exception('価格一覧の取得に失敗しました: ' . $e->getMessage());
        }
    }
}