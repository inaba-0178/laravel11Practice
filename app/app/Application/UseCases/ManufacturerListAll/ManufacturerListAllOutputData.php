<?php

namespace App\Application\UseCases\ManufacturerListAll;

class ManufacturerListAllOutputData
{
    public function __construct(
        private readonly array $groups
    ) {}

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'groups' => $this->groups,
            ],
        ];
    }
}