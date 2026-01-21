<?php
namespace App\Application\UseCases\GetRegionList;

use App\Application\UseCases\GetRegionList;
use App\Domain\GetRegionList\Repositories\GetRegionListRepository;

class GetRegionListInteractor implements GetRegionListUseCase {
    private GetRegionListRepository $repository;
    
    public function __construct(GetRegionListRepository $repository) {
        $this->repository = $repository;
    }
    
    public function handle(): GetRegionListOutputData {
        //$entities = $this->repository->findAllRegions();
        return new GetRegionListOutputData();
    }
}
