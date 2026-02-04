<?php

namespace App\Application\UseCases\SelectManufacturerList;

use App\Domain\SelectManufacturerList\Repositories\CarSerieRepositoryInterface;
use App\Domain\SelectManufacturerList\Repositories\ManufacturerRepositoryInterface;
use App\Domain\SelectManufacturerList\ValueObjects\ManufacturerName;
use Exception;

class SelectManufacturerListUseCase
{
    private CarSerieRepositoryInterface $carSerieRepository;
    private ManufacturerRepositoryInterface $manufacturerRepository; 

    public function __construct(
        CarSerieRepositoryInterface $carSerieRepository,
        ManufacturerRepositoryInterface $manufacturerRepository,
    )
    {
        $this->carSerieRepository = $carSerieRepository;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * topページボディタイプ一覧表示データ取得する
     * 
     * @return SelectManufacturerListOutputData
     * @throws Exception
     */
    public function execute(ManufacturerName $manufacturerName): SelectManufacturerListOutputData
    {
        try {
            $manufacturerId = $this->manufacturerRepository->findByName($manufacturerName->getValue());
            $carSeries = $this->carSerieRepository->findByManufacturerId($manufacturerId);

            return new SelectManufacturerListOutputData($carSeries);
        } catch (Exception $e) {
            throw new Exception('メーカー車両一覧表示データの取得に失敗しました: ' . $e->getMessage());
        }
    }
}