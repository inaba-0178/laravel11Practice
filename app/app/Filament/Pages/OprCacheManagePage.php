<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\NavigationGroup;
use App\Constants\RoleConstants;
use App\Domain\Common\Constants\CacheConstants;
use App\Domain\Common\Services\PermissionCacheService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class OprCacheManagePage extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $navigationIcon  = 'heroicon-o-circle-stack';
    protected static string  $view            = 'filament.pages.opr-cache-manage';
    protected static ?string $navigationGroup = NavigationGroup::SYSTEM_GROUP->value;
    protected static ?string $title           = 'キャッシュ管理';
    protected static ?int    $navigationSort  = 904;

    public static function canAccess(): bool
    {
        return auth()->user()?->role === RoleConstants::SUPER;
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('clearAll')
                ->label('全キャッシュクリア')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('全キャッシュをクリア')
                ->modalDescription('全てのキャッシュを削除します。次回アクセス時にDBから再取得されます。')
                ->action(function (): void {
                    Cache::flush();
                    Notification::make()->title('全キャッシュをクリアしました')->success()->send();
                }),
        ];
    }

    public function getCacheGroups(): array
    {
        return [
            [
                'id'       => 'permission',
                'name'     => '権限',
                'icon'     => 'heroicon-o-lock-closed',
                'color'    => 'danger',
                'keys'     => $this->withStatus([
                    ['key' => PermissionCacheService::CACHE_KEY, 'label' => '権限設定'],
                ]),
                'patterns' => [],
            ],
            [
                'id'       => 'mst',
                'name'     => 'マスタ',
                'icon'     => 'heroicon-o-table-cells',
                'color'    => 'primary',
                'keys'     => $this->withStatus([
                    ['key' => CacheConstants::KEY_BASIC_OPTIONS,        'label' => '基本オプション'],
                    ['key' => CacheConstants::KEY_BODY_TYPES,           'label' => 'ボディタイプ'],
                    ['key' => CacheConstants::KEY_COUNTRIES,            'label' => '国'],
                    ['key' => CacheConstants::KEY_MILEAGE_LIST,         'label' => '走行距離'],
                    ['key' => CacheConstants::KEY_PRICE_LIST,           'label' => '価格'],
                    ['key' => CacheConstants::KEY_RIDING_CAPACITY_LIST, 'label' => '乗車定員'],
                    ['key' => CacheConstants::KEY_AREAS,                'label' => 'エリア'],
                    ['key' => CacheConstants::KEY_MANUFACTURERS,        'label' => 'メーカー（一覧）'],
                    ['key' => CacheConstants::KEY_MANUFACTURERS_ACTIVE, 'label' => 'メーカー（アクティブ）'],
                    ['key' => CacheConstants::KEY_DISPLACEMENT_LIST,    'label' => '排気量'],
                ]),
                'patterns' => $this->withPatternCount([
                    'mst_manufacturers:*',
                    'mst_regions:*',
                    'mst_car_series:*',
                ]),
            ],
            [
                'id'       => 'option',
                'name'     => 'オプション',
                'icon'     => 'heroicon-o-tag',
                'color'    => 'success',
                'keys'     => $this->withStatus([
                    ['key' => CacheConstants::KEY_CAR_TYPE_OPTIONS,    'label' => '車種タイプ'],
                    ['key' => CacheConstants::KEY_COLOR_OPTIONS,        'label' => 'カラー'],
                    ['key' => CacheConstants::KEY_DETAIL_OPTIONS,       'label' => '詳細オプション'],
                    ['key' => CacheConstants::KEY_EQUIPMENT_BASIC,      'label' => '装備（基本）'],
                    ['key' => CacheConstants::KEY_EQUIPMENT_DRESSUP,    'label' => '装備（ドレスアップ）'],
                    ['key' => CacheConstants::KEY_EQUIPMENT_ENV,        'label' => '装備（環境）'],
                    ['key' => CacheConstants::KEY_EQUIPMENT_SAFETY,     'label' => '装備（安全）'],
                    ['key' => CacheConstants::KEY_SEAT_OPTIONS,         'label' => 'シート'],
                    ['key' => CacheConstants::KEY_LOAN_DOWN_OPTIONS,    'label' => 'ローン頭金'],
                    ['key' => CacheConstants::KEY_LOAN_MONTHLY_OPTIONS, 'label' => 'ローン月額'],
                ]),
                'patterns' => [],
            ],
            [
                'id'       => 'car',
                'name'     => '中古車',
                'icon'     => 'heroicon-o-truck',
                'color'    => 'warning',
                'keys'     => $this->withStatus([
                    ['key' => CacheConstants::KEY_CAR_COUNT, 'label' => '車両件数'],
                ]),
                'patterns' => $this->withPatternCount([
                    'car_list:*',
                    'car_detail:*',
                    'series_stk_count:*',
                    'car_condition_list:*',
                ]),
            ],
            [
                'id'       => 'search',
                'name'     => '検索',
                'icon'     => 'heroicon-o-magnifying-glass',
                'color'    => 'gray',
                'keys'     => [],
                'patterns' => $this->withPatternCount([
                    'price_histogram:*',
                ]),
            ],
        ];
    }

    public function clearKeyAction(): Action
    {
        return Action::make('clearKey')
            ->action(function (array $arguments): void {
                Cache::forget($arguments['key']);
                Notification::make()->title($arguments['label'] . ' をクリアしました')->success()->send();
            });
    }

    public function clearGroupAction(): Action
    {
        return Action::make('clearGroup')
            ->requiresConfirmation()
            ->modalHeading(fn(array $arguments) => $arguments['name'] . ' のキャッシュをクリア')
            ->action(function (array $arguments): void {
                foreach ($arguments['keys'] as $key) {
                    Cache::forget($key);
                }
                foreach ($arguments['patterns'] as $pattern) {
                    $this->clearByPattern($pattern);
                }
                Notification::make()
                    ->title($arguments['name'] . ' のキャッシュをクリアしました')
                    ->success()
                    ->send();
            });
    }

    private function withStatus(array $keys): array
    {
        $prefix = $this->redisPrefix();

        return array_map(function (array $item) use ($prefix): array {
            $exists    = Cache::has($item['key']);
            $ttl       = null;
            if ($exists) {
                $raw = Redis::ttl($prefix . $item['key']);
                $ttl = $raw >= 0 ? $raw : null;
            }
            return [...$item, 'exists' => $exists, 'ttl' => $ttl];
        }, $keys);
    }

    private function withPatternCount(array $patterns): array
    {
        $prefix = $this->redisPrefix();

        return array_map(function (string $pattern) use ($prefix): array {
            $count = count(Redis::keys($prefix . $pattern));
            return ['pattern' => $pattern, 'count' => $count];
        }, $patterns);
    }

    private function clearByPattern(string $pattern): void
    {
        $prefix = $this->redisPrefix();
        $keys   = Redis::keys($prefix . $pattern);
        if (!empty($keys)) {
            Redis::del($keys);
        }
    }

    private function redisPrefix(): string
    {
        return Cache::store('redis')->getStore()->getPrefix();
    }

    public static function formatTtl(?int $ttl): string
    {
        if ($ttl === null) return '∞';
        if ($ttl < 60)    return $ttl . '秒';
        if ($ttl < 3600)  return floor($ttl / 60) . '分';
        return floor($ttl / 3600) . '時間' . floor(($ttl % 3600) / 60) . '分';
    }
}
