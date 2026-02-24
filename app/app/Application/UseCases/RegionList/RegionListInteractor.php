<?php
namespace App\Application\UseCases\RegionList;

use App\Application\UseCases\RegionList;
use App\Domain\RegionList\Repositories\RegionListRepository;

class RegionListInteractor implements RegionListUseCase {
    private RegionListRepository $repository;
    
    public function __construct(RegionListRepository $repository) {
        $this->repository = $repository;
    }
    
    public function handle(): RegionListOutputData {
        //$entities = $this->repository->findAllRegions();
        return new RegionListOutputData();
    }
}
