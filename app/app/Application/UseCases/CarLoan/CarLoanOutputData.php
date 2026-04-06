<?php

namespace App\Application\UseCases\CarLoan;

class CarLoanOutputData
{
    public function __construct(
        private readonly ?array $data,
    ) {}

    public function toArray(): array
    {
        if ($this->data === null) {
            return [
                'success' => false,
                'loan'    => null,
            ];
        }

        return [
            'success' => true,
            'loan'    => $this->data,
        ];
    }
}