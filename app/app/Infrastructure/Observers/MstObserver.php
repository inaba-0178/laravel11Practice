<?php

declare(strict_types=1);

namespace App\Infrastructure\Observers;

use App\Application\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;

class MstObserver
{
    public function created(Model $model): void
    {
        AuditLogger::logModel($model, 'create');
    }

    public function updated(Model $model): void
    {
        AuditLogger::logModel($model, 'update');
    }

    public function deleted(Model $model): void
    {
        AuditLogger::logModel($model, 'delete');
    }
}
