<?php

namespace App\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

}