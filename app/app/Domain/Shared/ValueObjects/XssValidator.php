<?php

namespace App\Domain\Shared\ValueObjects;

final class XssValidator
{
    public static function check(string $value): bool
    {
        return preg_match('/<[^>]*>|javascript:|on\w+\s*=|<script|<\/script>/i', $value) === 1;
    }

    public static function sanitize(string $value): string
    {
        return htmlspecialchars(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}