<?php

namespace App\Domain\Shared\ValueObjects;

use App\Domain\Shared\ValueObjects\XssValidator;

final class MemberProfileValidator
{
    public static function validate(
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
    ): void {
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
        if ($address_line2 !== null && XssValidator::check($address_line2)) {
            throw new \InvalidArgumentException('使用できない文字が含まれています');
        }
    }
}