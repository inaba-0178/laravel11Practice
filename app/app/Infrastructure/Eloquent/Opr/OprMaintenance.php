<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Opr;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class OprMaintenance extends Model
{
    protected $connection = 'mst';
    protected $table      = 'opr_maintenance';

    public $timestamps = false;

    protected $fillable = [
        'is_maintenance',
        'message',
        'started_at',
        'estimated_end_at',
        'updated_by',
        'updated_at',
    ];

    protected $casts = [
        'is_maintenance'   => 'boolean',
        'started_at'       => 'datetime', 
        'estimated_end_at' => 'datetime',
        'updated_at'       => 'datetime',
    ];

    // ===== リレーション =====
    public function updatedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ===== ヘルパーメソッド =====
    public static function isMaintenance(): bool
    {
        return (bool) self::first()?->is_maintenance;
    }

    public static function getInstance(): self
    {
        return self::first() ?? self::create([
            'is_maintenance'   => false,
            'message'          => null,
            'started_at'       => null,
            'estimated_end_at' => null,
            'updated_by'       => null,
            'updated_at'       => now(),
        ]);
    }
}