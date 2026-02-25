<?php
namespace App\Domain\SelectAreaCarList\Exceptions;

use Exception;

class SelectAreaCarNotFoundException extends Exception
{
    public function __construct(int $seriesId, ?Exception $previous = null)
    {
        parent::__construct(
            "対象地域で車両ID '{$seriesId}' が見つかりませんでした。",
            404,
            $previous
        );
    }
}