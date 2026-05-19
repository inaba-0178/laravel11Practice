<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MstVersion extends Model
{
    protected $connection = 'mst';
    protected $table      = 'mst_versions';

    protected $fillable = [
        'version',
        'description',
        'status',
        'uploaded_by',
        'uploaded_at',
        'requested_by',
        'requested_at',
        'approved_by',
        'approved_at',
        'rejected_reason',
        'activated_by',
        'activated_at',
        'rolled_back_by',
        'rolled_back_at',
        'rollback_reason',
    ];

    protected $casts = [
        'uploaded_at'       => 'datetime',
        'requested_at'      => 'datetime',
        'approved_at'       => 'datetime',
        'activated_at'      => 'datetime',
        'rolled_back_at'    => 'datetime',
    ];

    // ===== リレーション =====

    public function uploadedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function requestedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function activatedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    // ===== ヘルパーメソッド =====

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function rolledBackBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'rolled_back_by');
    }

    /**
     * 次のバージョン番号を生成
     */
    public static function generateNextVersion(string $type = 'patch'): string
    {
        $latest = self::latest('id')->value('version') ?? '0.0.0';
        [$major, $minor, $patch] = array_map('intval', explode('.', $latest));

        return match($type) {
            'major' => ($major + 1) . '.0.0',
            'minor' => $major . '.' . ($minor + 1) . '.0',
            default => $major . '.' . $minor . '.' . ($patch + 1),
        };
    }
}