<?php
declare(strict_types=1);
namespace App\Domain\Inquiry\ValueObjects;
use InvalidArgumentException;

final class InquiryPhone
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if (!preg_match('/^[\d\-\+\(\)\s]{7,20}$/', $trimmed)) {
            throw new InvalidArgumentException('有効な電話番号を入力してください。');
        }
        $this->value = $trimmed;
    }
    public function getValue(): string { return $this->value; }
}