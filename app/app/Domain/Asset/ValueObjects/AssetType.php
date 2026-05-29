<?php

declare(strict_types=1);

namespace App\Domain\Asset\ValueObjects;

use InvalidArgumentException;

final class AssetType
{
    public const STAFF          = 'staff';
    public const CONTENT        = 'content';
    public const MEMBER         = 'member';
    public const OPR_MAIN_VIEW  = 'opr_main_view';

    private const ALLOWED = [
        self::STAFF,
        self::CONTENT,
        self::MEMBER,
        self::OPR_MAIN_VIEW,
    ];

    private readonly string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::ALLOWED, true)) {
            throw new InvalidArgumentException(
                'AssetTypeは次のいずれかである必要があります: ' . implode(', ', self::ALLOWED)
            );
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isStaff(): bool
    {
        return $this->value === self::STAFF;
    }

    public function isContent(): bool
    {
        return $this->value === self::CONTENT;
    }

    public function isMember(): bool
    {
        return $this->value === self::MEMBER;
    }

    public function isOprMainView(): bool
    {
        return $this->value === self::OPR_MAIN_VIEW;
    }
}