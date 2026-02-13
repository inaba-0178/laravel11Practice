<?php
namespace App\Domain\SelectBodyTypeList\Exceptions;

use Exception;

class BodyTypeNotFoundException extends Exception
{
    public function __construct(string $BodyTypeName, ?Exception $previous = null)
    {
        parent::__construct(
            "ボディタイプ '{$BodyTypeName}' が見つかりませんでした。",
            404,
            $previous
        );
    }
}