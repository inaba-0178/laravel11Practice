<?php
namespace App\Application\UseCases\AreaCarList;

class AreaCarListOutputData
{
    public function __construct(
        private readonly array $areaCarList,
    ) {}

    public function toArray(): array
    {

        return [
            'success'       => true,
            'areaCarData'   => $this->areaCarList,
        ];
    }
}