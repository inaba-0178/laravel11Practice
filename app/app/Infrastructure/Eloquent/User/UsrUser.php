<?php
namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsrUser extends Model
{
    use SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'usr_users';

    protected $primaryKey = 'id';
    public    $incrementing = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id',
        'status',
        'sei',
        'mei',
        'sei_kana',
        'mei_kana',
        'birth_date',
        'post_code',
        'prefecture',
        'city',
        'address_line1',
        'address_line2',
        'phone_number',
        'gender',
        'email',
        'email_verified_at',
        'email_changed_at',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_changed_at'  => 'datetime',
        'birth_date'        => 'date',
        'gender'            => 'integer',
    ];

    // フルネーム取得
    public function getFullNameAttribute(): string
    {
        return $this->sei . ' ' . $this->mei;
    }

    // フルネーム（カナ）取得
    public function getFullNameKanaAttribute(): string
    {
        return $this->sei_kana . ' ' . $this->mei_kana;
    }
}