<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\NavigationGroup;
use App\Constants\RoleConstants;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OprSystemHealthPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-heart';
    protected static string  $view            = 'filament.pages.opr-system-health';
    protected static ?string $navigationGroup = NavigationGroup::SYSTEM_GROUP->value;
    protected static ?string $title           = 'システムヘルス';
    protected static ?int    $navigationSort  = 905;

    public static function canAccess(): bool
    {
        return auth()->user()?->role === RoleConstants::SUPER;
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('更新')
                ->icon('heroicon-o-arrow-path')
                ->action(fn() => null),
        ];
    }

    public function getHealthChecks(): array
    {
        return [
            $this->checkDb('mst',        'MST DB'),
            $this->checkDb('user',       'ユーザー DB'),
            $this->checkDb('mst_backup', 'バックアップ DB'),
            $this->checkRedis(),
            $this->checkStorage(),
        ];
    }

    public function getAppInfo(): array
    {
        return [
            ['label' => 'PHP バージョン',      'value' => phpversion()],
            ['label' => 'Laravel バージョン',  'value' => app()->version()],
            ['label' => '環境 (APP_ENV)',      'value' => config('app.env')],
            ['label' => 'デバッグモード',        'value' => config('app.debug') ? '有効' : '無効',
             'warn'  => config('app.debug') && config('app.env') === 'production'],
            ['label' => 'キャッシュドライバー',  'value' => config('cache.default')],
            ['label' => 'タイムゾーン',         'value' => config('app.timezone')],
        ];
    }

    private function checkDb(string $connection, string $label): array
    {
        try {
            $start = microtime(true);
            DB::connection($connection)->select('SELECT 1');
            $ms     = $this->ms($start);
            $config = config("database.connections.{$connection}");

            return [
                'label'  => $label,
                'icon'   => 'heroicon-o-circle-stack',
                'status' => 'ok',
                'ms'     => $ms,
                'detail' => ($config['host'] ?? '?') . ':' . ($config['port'] ?? '?'),
            ];
        } catch (\Throwable $e) {
            return [
                'label'  => $label,
                'icon'   => 'heroicon-o-circle-stack',
                'status' => 'ng',
                'ms'     => null,
                'detail' => Str::limit($e->getMessage(), 100),
            ];
        }
    }

    private function checkRedis(): array
    {
        try {
            $start = microtime(true);
            Redis::ping();
            $ms = $this->ms($start);

            return [
                'label'  => 'Redis',
                'icon'   => 'heroicon-o-server',
                'status' => 'ok',
                'ms'     => $ms,
                'detail' => config('database.redis.default.host') . ':' . config('database.redis.default.port'),
            ];
        } catch (\Throwable $e) {
            return [
                'label'  => 'Redis',
                'icon'   => 'heroicon-o-server',
                'status' => 'ng',
                'ms'     => null,
                'detail' => Str::limit($e->getMessage(), 100),
            ];
        }
    }

    private function checkStorage(): array
    {
        try {
            $start = microtime(true);
            Storage::disk('s3')->exists('__health');
            $ms = $this->ms($start);

            return [
                'label'  => 'ストレージ (MinIO)',
                'icon'   => 'heroicon-o-archive-box',
                'status' => 'ok',
                'ms'     => $ms,
                'detail' => config('filesystems.disks.s3.endpoint') . '  /  bucket: ' . config('filesystems.disks.s3.bucket'),
            ];
        } catch (\Throwable $e) {
            return [
                'label'  => 'ストレージ (MinIO)',
                'icon'   => 'heroicon-o-archive-box',
                'status' => 'ng',
                'ms'     => null,
                'detail' => Str::limit($e->getMessage(), 100),
            ];
        }
    }

    private function ms(float $start): string
    {
        return round((microtime(true) - $start) * 1000, 1) . ' ms';
    }
}
