<?php

declare(strict_types=1);

namespace App\Domain\Asset\Exceptions;

use Exception;

class AssetRecordNotFoundException extends Exception
{
    public function __construct(string $type, int $recordId, ?Exception $previous = null)
    {
        parent::__construct(
            "タイプ '{$type}' のID '{$recordId}' が見つかりませんでした。",
            404,
            $previous
        );
    }
}