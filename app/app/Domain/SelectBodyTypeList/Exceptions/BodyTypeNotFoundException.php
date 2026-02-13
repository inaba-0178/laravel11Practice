<?php
namespace App\Domain\SelectBodyTypeList\Exceptions;

use Exception;

class BodyTypeNotFoundException extends Exception
{
    public function __construct(string $bodyTypeName, ?Exception $previous = null)
    {
        parent::__construct(
            "ボディタイプ '{$bodyTypeName}' が見つかりませんでした。",
            404,
            $previous
        );
    }
}