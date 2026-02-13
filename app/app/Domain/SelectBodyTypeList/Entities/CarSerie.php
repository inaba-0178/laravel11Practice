<?php

namespace App\Domain\SelectBodyTypeList\Entities;

class CarSerie
{
    private int     $seriesId;
    private ?string $seriesName;
    private int     $bodyTypeId;

    public function __construct(
        int     $seriesId,
        ?string $seriesName,
        int     $bodyTypeId,
    ) {
        $this->seriesId     = $seriesId;
        $this->seriesName   = $seriesName;
        $this->bodyTypeId   = $bodyTypeId;
    }

    public function getSeriesId(): int
    {
        return $this->seriesId;
    }

    public function getSeriesName(): string
    {
        return $this->seriesName;
    }

    public function getBodyTypeId(): int
    {
        return $this->bodyTypeId;
    }

    public function toArray(): array
    {
        return [
            'seriesId'      => $this->seriesId,
            'seriesName'    => $this->seriesName ?? '',
            'BodyTypeId'    => $this->bodyTypeId,
        ];
    }

}