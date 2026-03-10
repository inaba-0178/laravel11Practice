<?php

namespace App\Domain\Shared\Constants;

final class PasswordPolicy
{
    public const MIN_LENGTH                 = 12;
    public const RESET_TOKEN_EXPIRE_MINUTES = 30;
    public const RESET_TOKEN_LENGTH         = 64;
    public const HASH_ALGORITHM             = 'sha256';
}