<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerStaffData;

final class SelectDealerStaffDataOutputData
{
    public function __construct(
        private readonly array $staffs,
    ) {}

    public function toArray(): array
    {
        return [
            'success' => true,
            'staffs'  => array_map(fn ($staff) => $staff->toArray(), $this->staffs),
        ];
    }
}