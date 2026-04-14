<?php

declare(strict_types=1);

namespace App\Domain\SelectConditionCarList\Exceptions;

use Exception;

class SelectConditionCarNotFoundException extends Exception
{
    public function __construct(?Exception $previous = null)
    {
        parent::__construct(
            '条件に一致する車両が見つかりませんでした。',
            404,
            $previous
        );
    }
}