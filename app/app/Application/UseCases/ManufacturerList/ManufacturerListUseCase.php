<?php
namespace App\Application\UseCases\ManufacturerList;

use App\Domain\ManufacturerList\Repositories\ManufacturerRepositoryInterface;
use App\Domain\ManufacturerList\ValueObjects\ManufacturerIds;
use Exception;

class ManufacturerListUseCase
{
    public function __construct(
        private readonly ManufacturerRepositoryInterface $manufacturerRepository
    ) {}

    /**
     * メーカー一覧データ取得する
     * 
     * @return ManufacturerListOutputData
     * @throws Exception
     */
    public function execute(ManufacturerIds $manufacturerIds): ManufacturerListOutputData
    {
        try {
            $manufacturers = $this->manufacturerRepository->findByIds($manufacturerIds->getValue());
            
            // 結果が空の場合のハンドリング（オプション）
            if (empty($manufacturers)) {
                throw new Exception('指定されたIDのメーカーが見つかりませんでした');
            }
            
            return new ManufacturerListOutputData($manufacturers);
        } catch (Exception $e) {
            throw new Exception('メーカー一覧データの取得に失敗しました: ' . $e->getMessage());
        }
    }

    public function executeAll(): ManufacturerListOutputData
    {
        $manufacturers = $this->manufacturerRepository->findAll();
        return new ManufacturerListOutputData($manufacturers);
    }
}