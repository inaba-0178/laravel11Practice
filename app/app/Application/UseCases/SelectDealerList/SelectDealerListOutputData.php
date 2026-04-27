<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerList;

final class SelectDealerListOutputData
{
    public function __construct(
        private readonly array $dealers,
        private readonly int   $totalCount,
        private readonly int   $currentPage,
        private readonly int   $perPage,
    ) {}

    public function toArray(): array
    {
        return [
            'success'     => true,
            'dealers'     => array_map(fn ($dealer) => $dealer->toArray(), $this->dealers),
            'totalCount'  => $this->totalCount,
            'currentPage' => $this->currentPage,
            'perPage'     => $this->perPage,
            'totalPages'  => (int) ceil($this->totalCount / $this->perPage),
        ];
    }
}