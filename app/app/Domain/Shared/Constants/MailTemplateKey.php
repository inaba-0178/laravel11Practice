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
    // 車両登録承認：ディーラー担当者へ
    public const CAR_REGISTRATION_APPROVED_DEALER       = 'car_registration_approved_dealer';
    // 車両登録差し戻し：ディーラー担当者へ
    public const CAR_REGISTRATION_REJECTED_DEALER       = 'car_registration_rejected_dealer';

    public const USER_CREATED                           = 'user_created';

    // 問い合わせ返答：問い合わせ者へ
    public const INQUIRY_REPLIED                        = 'inquiry_replied';

    // チャット招待：ユーザーへ
    public const CHAT_INVITED                           = 'chat_invited';
    // チャット招待拒否：ディーラー担当者へ
    public const CHAT_INVITE_REJECTED                   = 'chat_invite_rejected';
    // チャット招待期限切れ：ディーラー担当者へ
    public const CHAT_INVITE_EXPIRED                    = 'chat_invite_expired';
}