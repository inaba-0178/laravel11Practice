<?php
declare(strict_types=1);
namespace App\Domain\Inquiry\ValueObjects;
use InvalidArgumentException;

final class InquiryMessage
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if (mb_strlen($trimmed) > 2000) {
            throw new InvalidArgumentException('メッセージは2000文字以内で入力してください。');
        }
        $this->value = $trimmed;
    }
    public function getValue(): string { return $this->value; }
}