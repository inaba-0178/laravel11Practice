<?php

namespace App\Infrastructure\Eloquent\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StkBulkUploadBatch extends Model
{
    use HasFactory;

    protected $connection = 'user';
    protected $table      = 'stk_bulk_upload_batches';

    protected $fillable = [
        'dealer_id',
        'uploaded_by',
        'uploaded_at',
        'approved_at',
        'total_count',
        'create_count',
        'update_count',
        'delete_count',
        'approved_count',
        'rejected_count',
        'pending_count',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function dealer()
    {
        return $this->belongsTo(StkCarDealer::class, 'dealer_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    public function cars()
    {
        return $this->hasMany(StkCar::class, 'bulk_batch_id');
    }

    /**
     * バッチのステータスを返す
     */
    public function getStatusAttribute(): string
    {
        if ($this->approved_at) {
            return 'approved';
        }
        if ($this->rejected_count > 0 && $this->pending_count === 0) {
            return 'rejected';
        }
        if ($this->rejected_count > 0 && $this->pending_count > 0) {
            return 'partial_rejected';
        }
        return 'pending';
    }

    /**
     * ステータスラベルを返す
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'approved'         => '承認済み',
            'rejected'         => '差し戻し',
            'partial_rejected' => '一部差し戻し',
            'pending'          => '承認確認中',
            default            => '不明',
        };
    }
}