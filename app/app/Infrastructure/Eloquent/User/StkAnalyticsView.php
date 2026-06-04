<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;

/**
 * 閲覧数記録モデル
 *
 * stk_analytics_viewsテーブルのEloquentモデル。
 * 車両ページ・ディーラーページの閲覧数を記録する。
 */
class StkAnalyticsView extends Model
{
    protected $connection = 'user';
    protected $table      = 'stk_analytics_views';

    protected $fillable = [
        'dealer_id',
        'car_id',
        'member_id',
        'cookie_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];
}