<?php
declare(strict_types=1);
namespace App\Domain\Inquiry\ValueObjects;
use InvalidArgumentException;

final class InquiryEmail
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if (!filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('有効なメールアドレスを入力してください。');
        }
        if (mb_strlen($trimmed) > 255) {
            throw new InvalidArgumentException('メールアドレスは255文字以内で入力してください。');
        }
        $this->value = $trimmed;
    }
    public function getValue(): string { return $this->value; }
}