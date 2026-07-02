<x-filament-panels::page>
    @php
        $items = $this->getUnregisteredClasses();
    @endphp

    <x-filament-actions::modals />

    @if ($items->isEmpty())
        <x-filament::section>
            <div class="flex items-center gap-3 text-success-600">
                <x-heroicon-o-check-circle class="w-6 h-6" />
                <span class="font-medium">未登録のページ・リソースはありません</span>
            </div>
        </x-filament::section>
    @else
        <x-filament::section>
            <x-slot name="heading">
                未登録 {{ $items->count() }} 件
            </x-slot>
            <x-slot name="description">
                権限管理に登録されていないページ・リソースです。登録するとロール別に表示制御できます。
            </x-slot>

            <div class="divide-y divide-gray-200 dark:divide-white/10">
                @foreach ($items->groupBy('resource_group') as $group => $groupItems)
                    <div class="py-3 first:pt-0">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            {{ $group }}
                        </div>
                        <div class="space-y-2">
                            @foreach ($groupItems as $item)
                                <div class="flex items-center justify-between gap-4 rounded-lg bg-gray-50 dark:bg-white/5 px-4 py-2">
                                    <div class="min-w-0">
                                        <div class="font-medium text-sm text-gray-900 dark:text-white">
                                            {{ $item['resource_label'] }}
                                        </div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">
                                            {{ $item['resource_key'] }}
                                        </div>
                                    </div>
                                    <button
                                        wire:click="openRegisterModal('{{ $item['resource_key'] }}', '{{ addslashes($item['resource_label']) }}', '{{ addslashes($item['resource_group']) }}')"
                                        type="button"
                                        class="shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-success-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-success-500 focus:outline-none focus:ring-2 focus:ring-success-500"
                                    >
                                        <x-heroicon-m-plus class="w-3.5 h-3.5" />
                                        登録
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
