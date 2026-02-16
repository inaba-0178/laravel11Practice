<?php

namespace App\Application\UseCases\ManufacturerList;

use App\Domain\ManufacturerList\Repositories\ManufacturerRepositoryInterface;
use App\Domain\ManufacturerList\ValueObjects\ManufacturerIds;
use Exception;

class ManufacturerListUseCase
{
    private ManufacturerRepositoryInterface $manufacturerRepository;

    public function __construct(
        ManufacturerRepositoryInterface $manufacturerRepository
    )
    {
        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * メーカー一覧データ取得する
     * 
     * @return ManufacturerListOutputData
     * @throws Exception
     */
    public function execute(ManufacturerIds $manufacturerIds) : ManufacturerListOutputData
    {
        try {            
            $getManufacturerIds = $this->manufacturerRepository->findByIds($manufacturerIds->getValue());
            return new ManufacturerListOutputData($getManufacturerIds);
        } catch (Exception $e) {
            throw new Exception('topページメーカー一覧表示データの取得に失敗しました: ' . $e->getMessage());
        }
    }
}