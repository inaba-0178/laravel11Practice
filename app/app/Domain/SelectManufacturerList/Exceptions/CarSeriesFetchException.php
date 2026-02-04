<?php
namespace App\Domain\SelectManufacturerList\Exceptions;

use Exception;

class CarSeriesFetchException extends Exception
{
    public function __construct(string $message = '', ?Exception $previous = null)
    {
        parent::__construct(
            $message ?: '車種一覧データの取得に失敗しました。',
            500,
            $previous
        );
    }
}