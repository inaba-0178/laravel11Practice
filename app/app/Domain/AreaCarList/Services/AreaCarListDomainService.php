<?php
namespace App\Domain\AreaCarList\Services;

class AreaCarListDomainService
{
    public function build(array $areas, array $regions, array $cars): array
    {
        // 1. regionId => count のマップを作成
        $carCountByRegion = [];
        foreach ($cars as $car) {
            $carCountByRegion[$car->getRegionId()] = ($carCountByRegion[$car->regionId] ?? 0) + 1;
        }

        // 2. areaCode => Region[] のマップを作成
        $regionsByArea = [];
        foreach ($regions as $region) {
            $regionsByArea[$region->getAreaCode()][] = $region;
        }

        // 3. Areaに紐づくRegionとcountをセット
        $result = [];
        foreach ($areas as $area) {
            $prefectures = [];
            $areaTotalCount = 0;

            foreach ($regionsByArea[$area->getId()] ?? [] as $region) {
                $count = $carCountByRegion[$region->getId()] ?? 0;
                $areaTotalCount += $count;
                $prefectures[] = [
                    'regionId'       => $region->getId(),
                    'prefectureName' => $region->getName(),
                    'count'          => $count,
                ];
            }

            $result[] = [
                'areaId'     => $area->getId(),
                'areaName'   => $area->getName(),
                'totalCount' => $areaTotalCount,
                'prefectures' => $prefectures,
            ];
        }

        return $result;
    }
}