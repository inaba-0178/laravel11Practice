<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;

final class EmailValidator
{
    public static function validate(string $email, string $field): string
    {
        $sanitized = XssValidator::sanitize($email);

        if (empty($sanitized)) {
            throw new InvalidArgumentException("{$field}は必須です。");
        }
        if (!filter_var($sanitized, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("{$field}の形式が正しくありません。");
        }
        if (strlen($sanitized) > 255) {
            throw new InvalidArgumentException("{$field}は255文字以内で入力してください。");
        }
        if (!preg_match('/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $sanitized)) {
            throw new InvalidArgumentException("{$field}の形式が正しくありません。");
        }

        return strtolower($sanitized);
    }
}