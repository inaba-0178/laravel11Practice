<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;

/**
 * usr_email_change_requests テーブルに対応するモデル
 *
 * @property int         $id
 * @property string      $usr_user_id
 * @property string      $current_email
 * @property string      $new_email
 * @property string      $current_email_token
 * @property string      $new_email_token
 * @property string|null $current_email_verified_at
 * @property string|null $new_email_verified_at
 * @property string      $current_email_token_expires_at
 * @property string      $new_email_token_expires_at
 * @property string      $created_at
 * @property string      $updated_at
 */
class EmailChangeRequest extends Model
{
    protected $connection = 'user';

    protected $table = 'usr_email_change_requests';

    protected $fillable = [
        'usr_user_id',
        'current_email',
        'new_email',
        'current_email_token',
        'new_email_token',
        'current_email_token_expires_at',
        'new_email_token_expires_at',
        'current_email_verified_at',
        'new_email_verified_at',
    ];

    protected $casts = [
        'current_email_token_expires_at' => 'datetime',
        'new_email_token_expires_at'     => 'datetime',
        'current_email_verified_at'      => 'datetime',
        'new_email_verified_at'          => 'datetime',
    ];
}