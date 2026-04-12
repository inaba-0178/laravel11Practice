<?php

declare(strict_types=1);

namespace App\Domain\Common\Repositories;

interface CarInterface
{
    public function toArray(): array;
    public function toCalculatorInput(): object;
}