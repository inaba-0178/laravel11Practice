<?php

namespace App\Infrastructure\Eloquent\Opr;

use Illuminate\Database\Eloquent\Model;

/**
 * システム設定モデル
 *
 * opr_settingsテーブルのEloquentモデル。
 * 閲覧カウント待機秒数などのシステム設定を管理する。
 */
class OprSetting extends Model
{
    protected $connection = 'mst';
    protected $table      = 'opr_settings';

    protected $fillable = [
        'key',
        'value',
        'label',
        'description',
    ];

    /**
     * キーで設定値を取得する
     *
     * @param string $key 設定キー
     * @param mixed $default デフォルト値
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }
}