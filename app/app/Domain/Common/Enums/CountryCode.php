<?php
declare(strict_types=1);
namespace App\Domain\Common\Enums;

enum CountryCode: string {
    case JP = 'JP';     // 日本
    case DE = 'DE';     // ドイツ
    case US = 'US';     // アメリカ
    case CA = 'CA';     // カナダ
    case GB = 'GB';     // イギリス
    case SE = 'SE';     // スウェーデン
    case FR = 'FR';     // フランス
    case IT = 'IT';     // イタリア
    case AT = 'AT';     // オーストリア
    case ES = 'ES';     // スペイン
    case SI = 'SI';     // スロベニア
    case RU = 'RU';     // ロシア
    case CN = 'CN';     // 中国
    case KR = 'KR';     // 韓国
    case MY = 'MY';     // マレーシア
    case ZA = 'ZA';     // 南アフリカ
    case XX = 'XX';     // その他
        
    public function label(): string {
        return match($this) {
            self::JP => '日本',
            self::DE => 'ドイツ',
            self::US => 'アメリカ',
            self::CA => 'カナダ',
            self::GB => 'イギリス',
            self::SE => 'スウェーデン',
            self::FR => 'フランス',
            self::IT => 'イタリア',
            self::AT => 'オーストリア',
            self::ES => 'スペイン',
            self::SI => 'スロベニア',
            self::RU => 'ロシア',
            self::CN => '中国',
            self::KR => '韓国',
            self::MY => 'マレーシア',
            self::ZA => '南アフリカ',
            self::XX => 'その他',
        };
    }
}
