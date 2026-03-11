<?php

namespace App\Domain\EditMember\ValueObjects;

use App\Domain\Shared\ValueObjects\MemberProfileValidator;

final class UpdateProfileRequest
{
    public function __construct(
        public readonly string  $sei,
        public readonly string  $mei,
        public readonly string  $sei_kana,
        public readonly string  $mei_kana,
        public readonly string  $birth_date,
        public readonly string  $post_code,
        public readonly string  $prefecture,
        public readonly string  $city,
        public readonly string  $address_line1,
        public readonly ?string $address_line2,
        public readonly string  $phone_number,
        public readonly int     $gender,
    ) {
        MemberProfileValidator::validate(
            sei:           $sei,
            mei:           $mei,
            sei_kana:      $sei_kana,
            mei_kana:      $mei_kana,
            birth_date:    $birth_date,
            post_code:     $post_code,
            prefecture:    $prefecture,
            city:          $city,
            address_line1: $address_line1,
            address_line2: $address_line2,
            phone_number:  $phone_number,
            gender:        $gender,
        );
    }
}