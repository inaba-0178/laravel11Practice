<?php

namespace App\Domain\Member\Entities;

final class ProvisionalRegistration
{
    private readonly string  $email;
    private readonly string  $token;
    private readonly ?string $created_at;

    public function __construct(
        string  $email,
        string  $token,
        ?string $created_at,
    ) {
        $this->email      = $email;
        $this->token      = $token;
        $this->created_at = $created_at;
    }

    public function getEmail(): string { return $this->email; }
    public function getToken(): string { return $this->token; }
    public function getCreatedAt(): ?string { return $this->created_at; }

    public function toArray(): array
    {
        return [
            'email'      => $this->email,
            'token'      => $this->token,
            'created_at' => $this->created_at,
        ];
    }
}