<?php
declare(strict_types=1);
namespace App\Domain\Inquiry\ValueObjects;
use InvalidArgumentException;

final class InquiryType
{
    private const ALLOWED = [
        'stock_check',
        'estimate',
        'condition_check',
        'other',
    ];

    private readonly string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::ALLOWED, true)) {
            throw new InvalidArgumentException('無効な問い合わせ種別です。');
        }
        $this->value = $value;
    }
    public function getValue(): string { return $this->value; }
}