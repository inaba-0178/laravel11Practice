<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;

class ProvisionalRegistration extends Model
{
    protected $connection = 'user';
    protected $table      = 'usr_provisional_registrations';
    protected $primaryKey = 'email';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];
}