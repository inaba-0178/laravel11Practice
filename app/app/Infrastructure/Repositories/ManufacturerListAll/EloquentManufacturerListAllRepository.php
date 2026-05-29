<?php

namespace App\Infrastructure\Repositories\ManufacturerListAll;

use App\Domain\ManufacturerListAll\Repositories\ManufacturerListAllRepositoryInterface;
use App\Domain\ManufacturerListAll\Repositories\CountryRepositoryInterface;
use App\Domain\ManufacturerListAll\Repositories\ManufacturerRepositoryInterface;
use App\Domain\ManufacturerListAll\Repositories\CarCountRepositoryInterface;

class EloquentManufacturerListAllRepository implements ManufacturerListAllRepositoryInterface
{
    public function __construct(
        private readonly CountryRepositoryInterface      $countryRepository,
        private readonly ManufacturerRepositoryInterface $manufacturerRepository,
        private readonly CarCountRepositoryInterface     $carCountRepository,
    ) {}

    public function findGrouped(): array
    {
        $countries     = $this->countryRepository->findAllActive();
        $manufacturers = $this->manufacturerRepository->findAllActive();
        $carCounts     = $this->carCountRepository->findAvailableCounts();

        $grouped = collect($manufacturers)->groupBy('countryCode');

        $result = [];
        foreach ($countries as $code => $country) {
            if (!$grouped->has($code)) continue;

            $makers = $grouped[$code]->map(fn($m) => [
                'id'          => $m['id'],
                'name'        => $m['name'],
                'displayName' => $m['displayName'],
                'code'        => $m['code'],
                'carCount'    => $carCounts[$m['id']] ?? 0,
            ])->values()->toArray();

            $result[] = [
                'countryCode' => $code,
                'label'       => $country['label'],
                'flag'        => $country['flag'],
                'anchor'      => $country['anchor'],
                'makers'      => $makers,
            ];
        }

        return $result;
    }
}