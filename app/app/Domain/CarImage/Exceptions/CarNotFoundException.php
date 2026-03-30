<?php

declare(strict_types=1);

namespace App\Domain\CarImage\Exceptions;

use Exception;

class CarNotFoundException extends Exception
{
    public function __construct(int $carId, ?Exception $previous = null)
    {
        parent::__construct(
            "車両ID '{$carId}' が見つかりませんでした。",
            404,
            $previous
        );
    }
}