<?php
namespace App\Application\UseCases\BodyTypeInfo;

use App\Domain\BodyTypeInfo\Repositories\BodyTypeRepositoryInterface;
use App\Domain\BodyTypeInfo\ValueObjects\BodyTypeName;
use App\Domain\BodyTypeInfo\Exceptions\BodyTypeNotFoundException;

class BodyTypeInfoUseCase
{
    public function __construct(
        private readonly BodyTypeRepositoryInterface $bodyTypeRepository,
    ) {}

    /**
     * 対象のボディタイプデータ取得する
     * 
     * @return BodyTypeInfoOutputData
     * @throws BodyTypeNotFoundException
     */
    public function execute(BodyTypeName $bodyTypeName): BodyTypeInfoOutputData
    {
        $bodyType = $this->bodyTypeRepository->findByBodyType($bodyTypeName->getValue());
        
        // リポジトリ層で例外を投げるべき（後述）
        return new BodyTypeInfoOutputData($bodyType);
    }
}