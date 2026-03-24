<?php
declare(strict_types=1);
namespace App\Domain\Shared\Constants;

final class MailTemplateKey
{
    public const PROVISIONAL_REGISTRATION               = 'provisional_registration';
    public const PASSWORD_RESET                         = 'password_reset';
    public const RESERVATION_CONFIRMED                  = 'reservation_confirmed';
    public const RESERVATION_DENIAL                     = 'reservation_denial';
    public const RESERVATION_CANCELLED_DEALER           = 'reservation_cancelled_dealer';
    public const RESERVATION_CANCELLED_CUSTOMER         = 'reservation_cancelled_customer';
    public const RESERVATION_CANCELLED_DEALER_TROUBLE   = 'reservation_cancelled_dealer_trouble';
}