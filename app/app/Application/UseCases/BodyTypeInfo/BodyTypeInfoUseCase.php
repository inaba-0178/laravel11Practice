<?php
namespace App\Application\UseCases\BodyTypeInfo;

use App\Domain\BodyTypeInfo\Repositories\BodyTypeRepositoryInterface;
use App\Domain\BodyTypeInfo\ValueObjects\BodyTypeName;
use App\Domain\BodyTypeInfo\Exceptions\BodyTypeNotFoundException;

class BodyTypeInfoUseCase
{
    private BodyTypeRepositoryInterface $bodyTypeRepository;

    public function __construct(
        BodyTypeRepositoryInterface $bodyTypeRepository,
    )
    {
        $this->bodyTypeRepository = $bodyTypeRepository;
    }

    /**
     * 対象のメーカーデータ取得する
     * 
     * @return BodyTypeInfoOutputData
     * @throws BodyTypeNotFoundException
     */
    public function execute(BodyTypeName $bodyTypeName) : BodyTypeInfoOutputData
    {
        try {
            $bodyType = $this->bodyTypeRepository->findByBodyType($bodyTypeName->getValue());

            // メーカーが見つからない場合
            if ($bodyType->getId() === null) {
                throw new BodyTypeNotFoundException($bodyTypeName->getValue());
            }
            return new BodyTypeInfoOutputData($bodyType);

        } catch (BodyTypeNotFoundException $e) {
            throw $e;
            
        }
    }
}