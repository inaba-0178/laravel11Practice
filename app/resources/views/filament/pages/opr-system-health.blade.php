<x-filament-panels::page>
    @php
        $checks  = $this->getHealthChecks();
        $appInfo = $this->getAppInfo();
        $allOk   = collect($checks)->every(fn($c) => $c['status'] === 'ok');
    @endphp

    {{-- 全体サマリー --}}
    <x-filament::section>
        <div class="flex items-center gap-3">
            @if ($allOk)
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-success-500"></span>
                </span>
                <span class="font-semibold text-success-600 dark:text-success-400">全サービス正常稼働中</span>
            @else
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-danger-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-danger-500"></span>
                </span>
                <span class="font-semibold text-danger-600 dark:text-danger-400">
                    {{ collect($checks)->where('status', 'ng')->count() }} サービスで障害を検出
                </span>
            @endif
            <span class="text-xs text-gray-400 ml-auto">{{ now()->format('Y/m/d H:i:s') }}</span>
        </div>
    </x-filament::section>

    {{-- サービスチェック --}}
    <x-filament::section heading="サービス接続">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ($checks as $check)
                <div @class([
                    'rounded-xl border p-4',
                    'border-success-200 bg-success-50 dark:border-success-500/30 dark:bg-success-500/10' => $check['status'] === 'ok',
                    'border-danger-200 bg-danger-50 dark:border-danger-500/30 dark:bg-danger-500/10'   => $check['status'] === 'ng',
                ])>
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <x-dynamic-component
                                :component="$check['icon']"
                                @class([
                                    'w-4 h-4',
                                    'text-success-500' => $check['status'] === 'ok',
                                    'text-danger-500'  => $check['status'] === 'ng',
                                ])
                            />
                            <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $check['label'] }}</span>
                        </div>
                        @if ($check['status'] === 'ok')
                            <span class="inline-flex items-center gap-1 rounded-full bg-success-100 dark:bg-success-500/20 text-success-700 dark:text-success-400 text-xs px-2 py-0.5 font-medium">
                                <x-heroicon-m-check class="w-3 h-3" />
                                正常
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-danger-100 dark:bg-danger-500/20 text-danger-700 dark:text-danger-400 text-xs px-2 py-0.5 font-medium">
                                <x-heroicon-m-x-mark class="w-3 h-3" />
                                エラー
                            </span>
                        @endif
                    </div>

                    <div class="mt-2 space-y-1">
                        @if ($check['ms'] !== null)
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                応答: <span class="font-mono">{{ $check['ms'] }}</span>
                            </div>
                        @endif
                        <div @class([
                            'text-xs break-all',
                            'text-gray-400 dark:text-gray-500' => $check['status'] === 'ok',
                            'text-danger-600 dark:text-danger-400' => $check['status'] === 'ng',
                        ])>
                            {{ $check['detail'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    {{-- アプリ情報 --}}
    <x-filament::section heading="アプリ情報">
        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3">
            @foreach ($appInfo as $item)
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $item['label'] }}</dt>
                    <dd @class([
                        'text-sm font-medium mt-0.5',
                        'text-warning-600 dark:text-warning-400' => $item['warn'] ?? false,
                        'text-gray-900 dark:text-white' => !($item['warn'] ?? false),
                    ])>
                        {{ $item['value'] }}
                        @if ($item['warn'] ?? false)
                            <x-heroicon-m-exclamation-triangle class="w-3.5 h-3.5 inline ml-1" />
                        @endif
                    </dd>
                </div>
            @endforeach
        </dl>
    </x-filament::section>
</x-filament-panels::page>
