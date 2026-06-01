<?php

namespace App\Domain\SelectBodyTypeList\Entities;

final class CarSerie
{
    private readonly int     $seriesId;
    private readonly ?string $seriesName;
    private readonly int     $manufacturerId;
    private readonly ?string $imageFilePath;

    public function __construct(
        int     $seriesId,
        ?string $seriesName,
        int     $manufacturerId,
        ?string $imageFilePath = null,
    ) {
        $this->seriesId         = $seriesId;
        $this->seriesName       = $seriesName;
        $this->manufacturerId   = $manufacturerId;
        $this->imageFilePath    = $imageFilePath;
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

    public function getImageFilePath(): ?string
    {
        return $this->imageFilePath;
    }

    public function toArray(): array
    {
        return [
            'seriesId'       => $this->seriesId,
            'seriesName'     => $this->seriesName ?? '',
            'manufacturerId' => $this->manufacturerId,
            'imageFilePath'  => $this->imageFilePath ?? '',
        ];
    }
}