<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, TwoFactorAuthenticatable;

    protected $connection = 'user';
    protected $table = 'users';

    protected $fillable = [
        'dealer_id',
        'role',
        'is_active',
        'is_public',
        'display_order',
        'name',
        'profile_image_url',
        'position',
        'bio',
        'specialty',
        'joined_at',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
        'is_public'         => 'boolean',
        'joined_at'         => 'date',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        // 有効なユーザーのみアクセス可能
        return $this->is_active;
    }

    // ロール判定
    public function isSuper(): bool
    {
        return $this->role === 'super';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDealer(): bool
    {
        return $this->role === 'dealer';
    }

    public function isDealerStaff(): bool
    {
        return $this->role === 'dealer_staff';
    }

    // dealer / dealer_staff かどうか
    public function isDealerRole(): bool
    {
        return in_array($this->role, ['dealer', 'dealer_staff']);
    }

    // 在籍年数の自動計算
    public function getYearsOfServiceAttribute(): ?int
    {
        if (!$this->joined_at) {
            return null;
        }
        return $this->joined_at->diffInYears(now());
    }
}