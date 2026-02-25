<?php

namespace App\Domain\SelectBodyTypeList\Entities;

final class CarSerie
{
    private readonly int     $seriesId;
    private readonly ?string $seriesName;
    private readonly int     $manufacturerId;

    public function __construct(
        int     $seriesId,
        ?string $seriesName,
        int     $manufacturerId,
    ) {
        $this->seriesId         = $seriesId;
        $this->seriesName       = $seriesName;
        $this->manufacturerId   = $manufacturerId;
    }

    public function getSeriesId(): int
    {
        return $this->seriesId;
    }

    public function getSeriesName(): string
    {
        return $this->seriesName;
    }

    public function getManufacturerId(): int
    {
        return $this->manufacturerId;
    }

    public function toArray(): array
    {
        return [
            'seriesId'          => $this->seriesId,
            'seriesName'        => $this->seriesName ?? '',
            'manufacturerId'    => $this->manufacturerId,
        ];
    }

}