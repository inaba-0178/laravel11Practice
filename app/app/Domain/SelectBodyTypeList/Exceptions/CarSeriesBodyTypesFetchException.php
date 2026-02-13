<?php
namespace App\Domain\SelectBodyTypeList\Exceptions;

use Exception;

class CarSeriesBodyTypesFetchException extends Exception
{
    public function __construct(string $message = '', ?Exception $previous = null)
    {
        parent::__construct(
            $message ?: 'ボディタイプデータの取得に失敗しました。',
            500,
            $previous
        );
    }
}