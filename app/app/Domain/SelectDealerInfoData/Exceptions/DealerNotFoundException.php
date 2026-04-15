<?php

declare(strict_types=1);

namespace App\Domain\SelectDealerInfoData\Exceptions;

use Exception;

class DealerNotFoundException extends Exception
{
    public function __construct(int $dealerId, ?Exception $previous = null)
    {
        parent::__construct(
            "販売店ID '{$dealerId}' が見つかりませんでした。",
            404,
            $previous
        );
    }
}