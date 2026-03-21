<?php

namespace App\Domain\SelectReservationType\Entities;

final class ReservationType
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $code,
        public readonly ?string $description,
        public readonly int     $isActive,
        public readonly int     $sortOrder,
    ) {}

    public function getId(): int { return $this->id; }
    public function getCode(): string { return $this->code; }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'code'        => $this->code,
            'description' => $this->description ?? '',
            'isActive'    => $this->isActive,
            'sortOrder'   => $this->sortOrder,
        ];
    }
}