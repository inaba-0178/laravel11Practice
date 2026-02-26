<?php
namespace App\Domain\SelectCarData\Exceptions;

use Exception;

class CarNotFoundException extends Exception
{
    public function __construct(int $seriesId, ?Exception $previous = null)
    {
        parent::__construct(
            "車両ID '{$seriesId}' が見つかりませんでした。",
            404,
            $previous
        );
    }
}