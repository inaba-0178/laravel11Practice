<?php

namespace App\Infrastructure\Eloquent\Mst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstCarSeriesImages extends Model
{
    use HasFactory;

    protected $connection = 'mst';
    protected $table      = 'mst_car_series_images';
    protected $primaryKey = 'id';

    protected $fillable = [
        'series_id',
        'file_path',
        'alt_text',
        'sort_order',
        'is_main',
        'is_active',
        'version_id',
    ];

    /**
     * 紐づくシリーズ
     */
    public function carSeries()
    {
        return $this->belongsTo(MstCarSeries::class, 'series_id', 'series_id');
    }
}