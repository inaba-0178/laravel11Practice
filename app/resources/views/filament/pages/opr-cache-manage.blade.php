<x-filament-panels::page>
    <x-filament-actions::modals />

    @php
        $groups = $this->getCacheGroups();
    @endphp

    <div class="space-y-4">
        @foreach ($groups as $group)
            @php
                $hasAnyKeys     = count($group['keys']) > 0;
                $hasAnyPatterns = count($group['patterns']) > 0;
                $allStaticKeys  = array_column($group['keys'], 'key');
                $allPatterns    = array_column($group['patterns'], 'pattern');
            @endphp

            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-dynamic-component :component="$group['icon']" class="w-5 h-5" />
                        {{ $group['name'] }}
                    </div>
                </x-slot>
                <x-slot name="headerEnd">
                    <button
                        wire:click="mountAction('clearGroup', {{ json_encode(['name' => $group['name'], 'keys' => $allStaticKeys, 'patterns' => $allPatterns]) }})"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-white/10"
                    >
                        <x-heroicon-m-arrow-path class="w-3.5 h-3.5" />
                        グループクリア
                    </button>
                </x-slot>

                @if ($hasAnyKeys)
                    <div class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($group['keys'] as $item)
                            <div class="flex items-center justify-between gap-4 py-2.5 first:pt-0 last:pb-0">
                                <div class="min-w-0 flex items-center gap-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['label'] }}</div>
                                        <div class="text-xs font-mono text-gray-400 mt-0.5">{{ $item['key'] }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    @if ($item['exists'])
                                        <span class="inline-flex items-center gap-1 rounded-full bg-success-100 dark:bg-success-500/20 text-success-700 dark:text-success-400 text-xs px-2 py-0.5 font-medium">
                                            <x-heroicon-m-check-circle class="w-3.5 h-3.5" />
                                            キャッシュ中
                                        </span>
                                        @if ($item['ttl'] !== null)
                                            <span class="text-xs text-gray-500">
                                                残り {{ \App\Filament\Pages\OprCacheManagePage::formatTtl($item['ttl']) }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400 text-xs px-2 py-0.5 font-medium">
                                            <x-heroicon-m-minus-circle class="w-3.5 h-3.5" />
                                            未キャッシュ
                                        </span>
                                    @endif
                                    <button
                                        wire:click="mountAction('clearKey', {{ json_encode(['key' => $item['key'], 'label' => $item['label']]) }})"
                                        type="button"
                                        @class([
                                            'inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-medium transition-colors',
                                            'bg-danger-50 dark:bg-danger-500/10 text-danger-600 dark:text-danger-400 hover:bg-danger-100' => $item['exists'],
                                            'bg-gray-100 dark:bg-white/5 text-gray-400 cursor-not-allowed' => !$item['exists'],
                                        ])
                                        @if (!$item['exists']) disabled @endif
                                    >
                                        <x-heroicon-m-x-mark class="w-3.5 h-3.5" />
                                        クリア
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($hasAnyPatterns)
                    <div @class(['mt-3 pt-3 border-t border-gray-100 dark:border-white/5' => $hasAnyKeys])>
                        <div class="text-xs text-gray-500 mb-2">動的キー</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($group['patterns'] as $p)
                                <div class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-3 py-1.5">
                                    <span class="font-mono text-xs text-gray-600 dark:text-gray-300">{{ $p['pattern'] }}</span>
                                    @if ($p['count'] > 0)
                                        <span class="rounded-full bg-warning-100 dark:bg-warning-500/20 text-warning-700 dark:text-warning-400 text-xs px-1.5 py-0.5 font-medium">
                                            {{ $p['count'] }}件
                                        </span>
                                    @else
                                        <span class="rounded-full bg-gray-100 dark:bg-white/10 text-gray-400 text-xs px-1.5 py-0.5">
                                            0件
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>
