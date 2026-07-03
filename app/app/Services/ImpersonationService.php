<?php

declare(strict_types=1);

namespace App\Services;

final class ImpersonationService
{
    private const SESSION_KEY_ID   = 'impersonate_dealer_id';
    private const SESSION_KEY_NAME = 'impersonate_dealer_name';

    public static function set(int $dealerId, string $dealerName): void
    {
        session([
            self::SESSION_KEY_ID   => $dealerId,
            self::SESSION_KEY_NAME => $dealerName,
        ]);
    }

    public static function clear(): void
    {
        session()->forget([self::SESSION_KEY_ID, self::SESSION_KEY_NAME]);
    }

    public static function isActive(): bool
    {
        return session()->has(self::SESSION_KEY_ID);
    }

    public static function getDealerId(): ?int
    {
        return session(self::SESSION_KEY_ID);
    }

    public static function getDealerName(): ?string
    {
        return session(self::SESSION_KEY_NAME);
    }
}
