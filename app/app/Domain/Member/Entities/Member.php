<?php

namespace App\Domain\Member\Entities;

final class Member
{
    private readonly string  $id;
    private readonly string  $sei;
    private readonly string  $mei;
    private readonly string  $sei_kana;
    private readonly string  $mei_kana;
    private readonly string  $birth_date;
    private readonly string  $post_code;
    private readonly string  $prefecture;
    private readonly string  $city;
    private readonly string  $address_line1;
    private readonly ?string $address_line2;
    private readonly string  $phone_number;
    private readonly int     $gender;
    private readonly string  $email;
    private readonly ?string $email_verified_at;
    private readonly ?string $email_changed_at;

    public function __construct(
        string  $id,
        string  $sei,
        string  $mei,
        string  $sei_kana,
        string  $mei_kana,
        string  $birth_date,
        string  $post_code,
        string  $prefecture,
        string  $city,
        string  $address_line1,
        ?string $address_line2,
        string  $phone_number,
        int     $gender,
        string  $email,
        ?string $email_verified_at,
        ?string $email_changed_at,
    ) {
        $this->id                = $id;
        $this->sei               = $sei;
        $this->mei               = $mei;
        $this->sei_kana          = $sei_kana;
        $this->mei_kana          = $mei_kana;
        $this->birth_date        = $birth_date;
        $this->post_code         = $post_code;
        $this->prefecture        = $prefecture;
        $this->city              = $city;
        $this->address_line1     = $address_line1;
        $this->address_line2     = $address_line2;
        $this->phone_number      = $phone_number;
        $this->gender            = $gender;
        $this->email             = $email;
        $this->email_verified_at = $email_verified_at;
        $this->email_changed_at  = $email_changed_at;
    }

    public function getId(): string { return $this->id; }
    public function getSei(): string { return $this->sei; }
    public function getMei(): string { return $this->mei; }
    public function getSeiKana(): string { return $this->sei_kana; }
    public function getMeiKana(): string { return $this->mei_kana; }
    public function getBirthDate(): string { return $this->birth_date; }
    public function getPostCode(): string { return $this->post_code; }
    public function getPrefecture(): string { return $this->prefecture; }
    public function getCity(): string { return $this->city; }
    public function getAddressLine1(): string { return $this->address_line1; }
    public function getAddressLine2(): ?string { return $this->address_line2; }
    public function getPhoneNumber(): string { return $this->phone_number; }
    public function getGender(): int { return $this->gender; }
    public function getEmail(): string { return $this->email; }
    public function getEmailVerifiedAt(): ?string { return $this->email_verified_at; }
    public function getEmailChangedAt(): ?string { return $this->email_changed_at; }

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'sei'               => $this->sei,
            'mei'               => $this->mei,
            'sei_kana'          => $this->sei_kana,
            'mei_kana'          => $this->mei_kana,
            'birth_date'        => $this->birth_date,
            'post_code'         => $this->post_code,
            'prefecture'        => $this->prefecture,
            'city'              => $this->city,
            'address_line1'     => $this->address_line1,
            'address_line2'     => $this->address_line2,
            'phone_number'      => $this->phone_number,
            'gender'            => $this->gender,
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'email_changed_at'  => $this->email_changed_at,
        ];
    }
}