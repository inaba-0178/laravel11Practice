<?php

namespace App\Domain\SelectBodyTypeList\Entities;

class CarSerie
{
    private int $seriesId;
    private ?string $seriesName;
    private int $BodyTypeId;

    public function __construct(
        int $seriesId,
        ?string $seriesName,
        int $BodyTypeId,
    ) {
        $this->seriesId = $seriesId;
        $this->seriesName = $seriesName;
        $this->BodyTypeId = $BodyTypeId;
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
        return $this->BodyTypeId;
    }

    public function toArray(): array
    {
        return [
            'seriesId' => $this->seriesId,
            'seriesName' => $this->seriesName ?? '',
            'BodyTypeId' => $this->BodyTypeId,
        ];
    }

}