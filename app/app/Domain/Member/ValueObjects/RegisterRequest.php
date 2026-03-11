<?php

namespace App\Domain\Member\ValueObjects;

use App\Domain\Shared\Constants\PasswordPolicy;

final class RegisterRequest
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
        public readonly string  $email,
        public readonly string  $token,
        public readonly string  $password,
    ) {
        if (empty($sei)) {
            throw new \InvalidArgumentException('苗字は必須です');
        }
        if (empty($mei)) {
            throw new \InvalidArgumentException('名前は必須です');
        }
        if (empty($sei_kana)) {
            throw new \InvalidArgumentException('苗字カナは必須です');
        }
        if (empty($mei_kana)) {
            throw new \InvalidArgumentException('名前カナは必須です');
        }
        if (empty($birth_date)) {
            throw new \InvalidArgumentException('生年月日は必須です');
        }
        if (empty($post_code)) {
            throw new \InvalidArgumentException('郵便番号は必須です');
        }
        if (empty($prefecture)) {
            throw new \InvalidArgumentException('都道府県は必須です');
        }
        if (empty($city)) {
            throw new \InvalidArgumentException('市区町村は必須です');
        }
        if (empty($address_line1)) {
            throw new \InvalidArgumentException('番地は必須です');
        }
        if (empty($phone_number)) {
            throw new \InvalidArgumentException('電話番号は必須です');
        }
        if (empty($email)) {
            throw new \InvalidArgumentException('メールアドレスは必須です');
        }
        if (empty($token)) {
            throw new \InvalidArgumentException('トークンが無効です');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('メールアドレスの形式が正しくありません');
        }
        if (strlen($password) < PasswordPolicy::MIN_LENGTH) {
            throw new \InvalidArgumentException('パスワードは' . PasswordPolicy::MIN_LENGTH . '文字以上で入力してください');
        }
        if (!preg_match('/^[0-9]{7}$/', $post_code)) {
            throw new \InvalidArgumentException('郵便番号は7桁の数字で入力してください');
        }
        if (!preg_match('/^[0-9]{10,11}$/', $phone_number)) {
            throw new \InvalidArgumentException('電話番号は10桁または11桁の数字で入力してください');
        }
    }
}