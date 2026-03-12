<?php

namespace App\Application\UseCases\EditMember;

use App\Domain\Member\Entities\Member;

class GetProfileOutputData
{
    private string  $id;
    private string  $sei;
    private string  $mei;
    private string  $sei_kana;
    private string  $mei_kana;
    private string  $birth_date;
    private string  $post_code;
    private string  $prefecture;
    private string  $city;
    private string  $address_line1;
    private ?string $address_line2;
    private string  $phone_number;
    private int     $gender;
    private string  $email;
    private ?string $email_verified_at;
    private ?string $email_changed_at;

    public function __construct(Member $member)
    {
        $this->id                = $member->getId();
        $this->sei               = $member->getSei();
        $this->mei               = $member->getMei();
        $this->sei_kana          = $member->getSeiKana();
        $this->mei_kana          = $member->getMeiKana();
        $this->birth_date        = $member->getBirthDate();
        $this->post_code         = $member->getPostCode();
        $this->prefecture        = $member->getPrefecture();
        $this->city              = $member->getCity();
        $this->address_line1     = $member->getAddressLine1();
        $this->address_line2     = $member->getAddressLine2();
        $this->phone_number      = $member->getPhoneNumber();
        $this->gender            = $member->getGender();
        $this->email             = $this->maskEmail($member->getEmail());
        $this->email_verified_at = $member->getEmailVerifiedAt();
        $this->email_changed_at  = $member->getEmailChangedAt();
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);
        $masked = substr($local, 0, 2) . '****';
        return $masked . '@' . $domain;
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
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
            ],
        ];
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
}