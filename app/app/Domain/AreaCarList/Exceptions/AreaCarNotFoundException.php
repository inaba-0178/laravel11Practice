<?php
namespace App\Domain\AreaCarList\Exceptions;

use Exception;

class AreaCarNotFoundException extends Exception
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