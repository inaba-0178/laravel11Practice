<?php
namespace App\Application\UseCases\SelectManufacturerList;

use App\Domain\SelectManufacturerList\Repositories\CarSerieRepositoryInterface;
use App\Domain\SelectManufacturerList\Repositories\ManufacturerRepositoryInterface;
use App\Domain\SelectManufacturerList\ValueObjects\ManufacturerName;
use App\Domain\SelectManufacturerList\Exceptions\ManufacturerNotFoundException;
use App\Domain\SelectManufacturerList\Exceptions\CarSeriesFetchException;
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
     * メーカー車種一覧表示データ取得する
     * 
     * @return SelectManufacturerListOutputData
     * @throws ManufacturerNotFoundException
     * @throws CarSeriesFetchException
     */
    public function execute(ManufacturerName $manufacturerName): SelectManufacturerListOutputData
    {
        try {
            $manufacturerId = $this->manufacturerRepository->findByName($manufacturerName->getValue());
            
            // メーカーが見つからない場合
            if ($manufacturerId === null) {
                throw new ManufacturerNotFoundException($manufacturerName->getValue());
            }
            
            $carSeries = $this->carSerieRepository->findByManufacturerId($manufacturerId);
            
            return new SelectManufacturerListOutputData($carSeries);
            
        } catch (ManufacturerNotFoundException $e) {
            // メーカー未検出の例外はそのまま再スロー
            throw $e;
            
        } catch (Exception $e) {
            // その他の例外は専用例外でラップ
            throw new CarSeriesFetchException(
                'メーカー車両一覧表示データの取得に失敗しました: ' . $e->getMessage(),
                $e  // 元の例外を保持
            );
        }
    }
}