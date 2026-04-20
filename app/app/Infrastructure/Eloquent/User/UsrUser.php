<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UsrUser extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $connection = 'user';
    protected $table      = 'usr_users';
    protected $keyType    = 'string';
    public $incrementing  = false;

    protected $fillable = [
        'id',
        'nickname',
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
        'password',
    ];

    protected $casts = [
        'birth_date'        => 'date',
        'email_verified_at' => 'datetime',
        'email_changed_at'  => 'datetime',
        'gender'            => 'integer',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->sei . ' ' . $this->mei;
    }

    public function getFullNameKanaAttribute(): string
    {
        return $this->sei_kana . ' ' . $this->mei_kana;
    }

    public function reviews()
    {
        return $this->hasMany(StkDealerReview::class, 'member_id');
    }

}