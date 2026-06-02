<?php

namespace App\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Constants\MstTableMap;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function toEntities(Collection $models, callable $toEntity): array
    {
        return $models->map($toEntity)->all();
    }

    /**
     * DBに保存されたファイル名からMinIO/S3のフルURLを生成する
     * 例: LEXUS/ct.jpg → {S3_URL}/mst/car_series/LEXUS/ct.jpg
     */
    protected function buildS3Url(string $tableName, string $filePath): string
    {
        if (empty($filePath)) return '';

        $folder  = MstTableMap::getImageFolder($tableName);
        $baseUrl = rtrim((string) config('filesystems.disks.s3.url'), '/');

        return "{$baseUrl}/{$folder}{$filePath}";
    }
}