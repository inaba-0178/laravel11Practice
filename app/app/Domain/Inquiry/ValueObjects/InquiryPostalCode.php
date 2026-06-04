<?php
declare(strict_types=1);
namespace App\Domain\Inquiry\ValueObjects;
use InvalidArgumentException;

final class InquiryPostalCode
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $trimmed = preg_replace('/[^\d]/', '', $value);
        if (!preg_match('/^\d{7}$/', $trimmed)) {
            throw new InvalidArgumentException('郵便番号は7桁の数字で入力してください。');
        }
        $this->value = $trimmed;
    }
    public function getValue(): string { return $this->value; }
}