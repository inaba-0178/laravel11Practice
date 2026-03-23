<?php

namespace App\Domain\CreateReservation\Exceptions;

use Exception;

class ScheduleFullException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            '申し訳ございません。選択された日時はすでに予約が埋まってしまいました。別の日時をお選びください。',
            409,
        );
    }
}