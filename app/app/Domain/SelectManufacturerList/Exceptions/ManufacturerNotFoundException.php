<?php
namespace App\Domain\SelectManufacturerList\Exceptions;

use Exception;

class ManufacturerNotFoundException extends Exception
{
    public function __construct(string $manufacturerName, ?Exception $previous = null)
    {
        parent::__construct(
            "メーカー '{$manufacturerName}' が見つかりませんでした。",
            404,
            $previous
        );
    }
}