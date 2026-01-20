<?php

namespace App\Application\UseCases\GetRegionList;

class GetRegionListInputData
{
    private bool $activeOnly;

    public function __construct(bool $activeOnly = false)
    {
        $this->activeOnly = $activeOnly;
    }

    public function isActiveOnly(): bool
    {
        return $this->activeOnly;
    }
}