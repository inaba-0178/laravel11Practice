<?php
namespace App\Application\UseCases\SelectAreaCarList;

use App\Domain\SelectAreaCarList\Entities\Car;

class SelectAreaCarListOutputData
{
    private readonly int $count;
    public function __construct(
        private readonly array $carLists,
    ) {
        $this->count = count($carLists);
    }

    public function toArray(): array
    {

        return [
            'success'   => true,
            'carList'   => array_map(fn(Car $car) => $car->toArray(), $this->carLists),
            'count'     => $this->count,
        ];
    }

    public function getCarList(): array
    {
        return $this->carLists;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}