<?php

namespace App\Domain\SelectBodyTypeList\Entities;

class CarSeriesBodyType
{
    private int $id;
    private int $seriesId;
    private int $bodyTypeId;

    public function __construct(
        int $id,
        int $seriesId,
        int $bodyTypeId,
    ) {
        $this->id           = $id;
        $this->seriesId     = $seriesId;
        $this->bodyTypeId   = $bodyTypeId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSeriesId(): int
    {
        return $this->seriesId;
    }

    public function getBodyTypeId(): int
    {
        return $this->bodyTypeId;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->seriesId,
            'seriesId'      => $this->seriesId,
            'bodyTypeId'    => $this->bodyTypeId,
        ];
    }

}