<?php

namespace App\Domain\SeriesStkCount\ValueObjects;

class SeriesStkCountRequest
{
    public readonly array $seriesIds;

    public function __construct(array $seriesIds)
    {
        if (empty($seriesIds)) {
            throw new \InvalidArgumentException('seriesIdsは1件以上指定してください');
        }

        $this->seriesIds = array_map('intval', $seriesIds);
    }
}