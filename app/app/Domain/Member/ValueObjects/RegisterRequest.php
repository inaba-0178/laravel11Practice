<?php

namespace App\Domain\Member\ValueObjects;

use App\Domain\Shared\Constants\PasswordPolicy;
use App\Domain\Shared\ValueObjects\XssValidator;

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
        // 必須チェック
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

        // 形式チェック
        if (!preg_match('/^[\p{Han}\p{Hiragana}\p{Katakana}ー々〆〤]+$/u', $sei)) {
            throw new \InvalidArgumentException('苗字は日本語で入力してください');
        }
        if (!preg_match('/^[\p{Han}\p{Hiragana}\p{Katakana}ー々〆〤]+$/u', $mei)) {
            throw new \InvalidArgumentException('名前は日本語で入力してください');
        }
        if (!preg_match('/^[ァ-ヶー]+$/u', $sei_kana)) {
            throw new \InvalidArgumentException('苗字カナは全角カタカナで入力してください');
        }
        if (!preg_match('/^[ァ-ヶー]+$/u', $mei_kana)) {
            throw new \InvalidArgumentException('名前カナは全角カタカナで入力してください');
        }
        if (!strtotime($birth_date) || strtotime($birth_date) > time()) {
            throw new \InvalidArgumentException('生年月日が正しくありません');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('メールアドレスの形式が正しくありません');
        }
        if (!preg_match('/^[0-9]{7}$/', $post_code)) {
            throw new \InvalidArgumentException('郵便番号は7桁の数字で入力してください');
        }
        if (!preg_match('/^[\p{Han}\p{Hiragana}\p{Katakana}ー々\p{N}]+$/u', $prefecture)) {
            throw new \InvalidArgumentException('都道府県は日本語で入力してください');
        }
        if (!preg_match('/^[\p{Han}\p{Hiragana}\p{Katakana}ー々\p{N}\-]+$/u', $city)) {
            throw new \InvalidArgumentException('市区町村に使用できない文字が含まれています');
        }
        if (!preg_match('/^[\p{Han}\p{Hiragana}\p{Katakana}ー々\p{N}\-ー丁目番地号]+$/u', $address_line1)) {
            throw new \InvalidArgumentException('番地に使用できない文字が含まれています');
        }
        if ($address_line2 !== null && !preg_match('/^[\p{Han}\p{Hiragana}\p{Katakana}ー々\p{N}\-A-Za-z\s　]+$/u', $address_line2)) {
            throw new \InvalidArgumentException('建物名に使用できない文字が含まれています');
        }
        if (!preg_match('/^[0-9]{10,11}$/', $phone_number)) {
            throw new \InvalidArgumentException('電話番号は10桁または11桁の数字で入力してください');
        }
        if (!in_array($gender, [0, 1, 2], true)) {
            throw new \InvalidArgumentException('性別の値が正しくありません');
        }
        if (strlen($password) < PasswordPolicy::MIN_LENGTH) {
            throw new \InvalidArgumentException('パスワードは' . PasswordPolicy::MIN_LENGTH . '文字以上で入力してください');
        }
        if (XssValidator::check($password)) {
            throw new \InvalidArgumentException('使用できない文字が含まれています');
        }
        if ($address_line2 !== null && XssValidator::check($address_line2)) {
            throw new \InvalidArgumentException('使用できない文字が含まれています');
        }
    }
}