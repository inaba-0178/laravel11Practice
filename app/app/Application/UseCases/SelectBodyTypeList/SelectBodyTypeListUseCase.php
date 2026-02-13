<?php
namespace App\Application\UseCases\SelectBodyTypeList;

use App\Domain\SelectBodyTypeList\Repositories\CarSeriesBodyTypeRepositoryInterface;
use App\Domain\SelectBodyTypeList\Repositories\CarSerieRepositoryInterface;
use App\Domain\SelectBodyTypeList\Repositories\BodyTypeRepositoryInterface;
use App\Domain\SelectBodyTypeList\ValueObjects\BodyTypeName;
use App\Domain\SelectBodyTypeList\Exceptions\BodyTypeNotFoundException;
use App\Domain\SelectBodyTypeList\Exceptions\CarSeriesFetchException;
use App\Domain\Common\Services\JapaneseInitialGroupingService;
use Exception;

class SelectBodyTypeListUseCase
{
    private CarSeriesBodyTypeRepositoryInterface    $carSeriesBodyType;
    private CarSerieRepositoryInterface             $carSerieRepository;
    private BodyTypeRepositoryInterface             $bodyTypeRepository;
    private JapaneseInitialGroupingService          $groupingService;

    public function __construct(
        CarSeriesBodyTypeRepositoryInterface    $carSeriesBodyTypeRepository,
        CarSerieRepositoryInterface             $carSerieRepository,
        BodyTypeRepositoryInterface             $bodyTypeRepository,
        //JapaneseInitialGroupingService          $groupingService
    )
    {
        $this->carSeriesBodyTypeRepository  = $carSeriesBodyTypeRepository;
        $this->carSerieRepository           = $carSerieRepository;
        $this->bodyTypeRepository           = $bodyTypeRepository;
        //$this->groupingService              = $groupingService;
    }

    /**
     * メーカー車種一覧表示データ取得する
     * 
     * @return SelectBodyTypeListOutputData
     * @throws BodyTypeNotFoundException
     * @throws CarSeriesFetchException
     */
    public function execute(BodyTypeName $bodyTypeName) //: SelectBodyTypeListOutputData
    {
        try {
            $bodyType = $this->bodyTypeRepository->findByBodyType($bodyTypeName->getValue());

            // メーカーが見つからない場合
            if ($bodyType->getId() === null) {
                throw new BodyTypeNotFoundException($bodyTypeName->getValue());
            }
            $carSeriesBody = $this->carSeriesBodyTypeRepository->findBySeriesBodyTypeId($bodyType->getId());
            
            $seriesIds = $carSeriesBody->pluck('series_id')->all();
            $carSeries = $this->carSerieRepository->findByCarSeries($seriesIds);

            // // グルーピング実行
            // $groupedCarSeries = $this->groupingService->groupByInitial($carSeries);

            return new SelectBodyTypeListOutputData($carSeries);
            
        } catch (BodyTypeNotFoundException $e) {
            throw $e;
            
        } catch (Exception $e) {
            throw new CarSeriesFetchException(
                'メーカー車両一覧表示データの取得に失敗しました: ' . $e->getMessage(),
                $e
            );
        }
    }
}