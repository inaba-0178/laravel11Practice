<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasUuids, SoftDeletes;

    protected $connection = 'user';
    protected $table = 'usr_users';

    protected $fillable = [
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

    protected function casts(): array
    {
        return [
            'birth_date'        => 'date',
            'email_verified_at' => 'datetime',
            'email_changed_at'  => 'datetime',
            'password'          => 'hashed',
            'deleted_at'        => 'datetime',
        ];
    }
}