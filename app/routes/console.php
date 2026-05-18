<?php

use App\Infrastructure\Eloquent\Opr\OprMaintenance;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1分ごとにメンテナンス状態をチェック
Schedule::call(function () {
    try {
        \Log::info('scheduler called', ['time' => now()->toDateTimeString()]);
        
        $maintenance = OprMaintenance::getInstance();
        $now         = now();

        \Log::info('maintenance check', [
            'started_at'     => $maintenance->getRawOriginal('started_at'),
            'is_maintenance' => $maintenance->is_maintenance,
        ]);

        if ($maintenance->started_at && $now->gte($maintenance->started_at) && !$maintenance->is_maintenance) {
            \Log::info('turning ON');
            $maintenance->update(['is_maintenance' => true]);
        }

        if ($maintenance->estimated_end_at && $now->gte($maintenance->estimated_end_at) && $maintenance->is_maintenance) {
            \Log::info('turning OFF');
            $maintenance->update([
                'is_maintenance'   => false,
                'started_at'       => null,
                'estimated_end_at' => null,
            ]);
        }
    } catch (\Throwable $e) {
        \Log::error('scheduler error', ['message' => $e->getMessage()]);
    }
})->everyMinute();