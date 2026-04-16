<?php

declare(strict_types=1);

namespace App\Domain\StaffImage\Exceptions;

use Exception;

class StaffNotFoundException extends Exception
{
    public function __construct(int $staffId, ?Exception $previous = null)
    {
        parent::__construct(
            "スタッフID '{$staffId}' が見つかりませんでした。",
            404,
            $previous
        );
    }
}