<?php

namespace App\Application\UseCases\DisplacementList;

use App\Domain\DisplacementList\Entities\Displacement;

class DisplacementListOutputData
{
    private ?array $displacements;
    private int $count;

    /**
     * @param Displacement[] $displacements
     */
    public function __construct(array $displacements)
    {
        $this->displacements = $displacements;
        $this->count = count($displacements);
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'DisplacementList' => array_map(fn(Displacement $displacement) => $displacement->toArray(), $this->displacements),
                'count' => $this->count,
            ],
        ];
    }

    public function Displacements(): array
    {
        return $this->displacements;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}