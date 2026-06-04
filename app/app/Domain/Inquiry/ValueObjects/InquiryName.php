<?php
declare(strict_types=1);
namespace App\Domain\Inquiry\ValueObjects;
use InvalidArgumentException;

final class InquiryName
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if (mb_strlen($trimmed) === 0) {
            throw new InvalidArgumentException('名前を入力してください。');
        }
        if (mb_strlen($trimmed) > 100) {
            throw new InvalidArgumentException('名前は100文字以内で入力してください。');
        }
        $this->value = $trimmed;
    }
    public function getValue(): string { return $this->value; }
}