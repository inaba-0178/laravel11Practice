<?php

declare(strict_types=1);

namespace App\Domain\SelectRegionCarList\Exceptions;

use Exception;

class SelectRegionCarNotFoundException extends Exception
{
    public function __construct(int $regionId, ?Exception $previous = null)
    {
        parent::__construct(
            "地域ID '{$regionId}' の車両が見つかりませんでした。",
            404,
            $previous
        );
    }
}