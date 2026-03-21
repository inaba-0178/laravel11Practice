<?php
namespace App\Domain\CreateReservation\Exceptions;
use Exception;

class PastTimeException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            '大変申し訳ございません。選択された時間はすでに過ぎています。別の時間をお選びください。',
            409,
        );
    }
}