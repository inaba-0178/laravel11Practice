<?php

namespace App\Domain\Shared\ValueObjects;

final class XssValidator
{
    public static function check(string $value): bool
    {
        return preg_match('/<[^>]*>|javascript:|on\w+\s*=|<script|<\/script>/i', $value) === 1;
    }
}