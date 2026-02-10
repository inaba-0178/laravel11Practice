<?php
namespace App\Application\UseCases\SelectManufacturerList;

use App\Domain\SelectManufacturerList\Repositories\CarSerieRepositoryInterface;
use App\Domain\SelectManufacturerList\Repositories\ManufacturerRepositoryInterface;
use App\Domain\SelectManufacturerList\ValueObjects\ManufacturerName;
use App\Domain\SelectManufacturerList\Exceptions\ManufacturerNotFoundException;
use App\Domain\SelectManufacturerList\Exceptions\CarSeriesFetchException;
use App\Domain\Common\Services\JapaneseInitialGroupingService;
use Exception;

class SelectManufacturerListUseCase
{
    private CarSerieRepositoryInterface $carSerieRepository;
    private ManufacturerRepositoryInterface $manufacturerRepository;
    private JapaneseInitialGroupingService $groupingService;

    public function __construct(
        CarSerieRepositoryInterface $carSerieRepository,
        ManufacturerRepositoryInterface $manufacturerRepository,
        JapaneseInitialGroupingService $groupingService
    )
    {
        $this->carSerieRepository = $carSerieRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->groupingService = $groupingService;
    }

    /**
     * メーカー車種一覧表示データ取得する
     * 
     * @return SelectManufacturerListOutputData
     * @throws ManufacturerNotFoundException
     * @throws CarSeriesFetchException
     */
    public function execute(ManufacturerName $manufacturerName): SelectManufacturerListOutputData
    {
        try {
            $manufacturer = $this->manufacturerRepository->findByName($manufacturerName->getValue());
            
            // メーカーが見つからない場合
            if ($manufacturer === null) {
                throw new ManufacturerNotFoundException($manufacturerName->getValue());
            }
            
            $carSeries = $this->carSerieRepository->findByManufacturerId($manufacturer->getId());

            // グルーピング実行
            $groupedCarSeries = $this->groupingService->groupByInitial($carSeries);

            return new SelectManufacturerListOutputData($manufacturer, $carSeries, $groupedCarSeries);
            
        } catch (ManufacturerNotFoundException $e) {
            throw $e;
            
        } catch (Exception $e) {
            throw new CarSeriesFetchException(
                'メーカー車両一覧表示データの取得に失敗しました: ' . $e->getMessage(),
                $e
            );
        }
    }
}