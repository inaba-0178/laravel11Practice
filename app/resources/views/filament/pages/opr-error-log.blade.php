<x-filament-panels::page>
    <x-filament-actions::modals />

    @php
        $fileInfo = $this->getLogFileInfo();
        $entries  = $this->getLogEntries();
        $levels   = ['', 'ERROR', 'CRITICAL', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'];
        $levelLabels = ['' => '全レベル', 'ERROR' => 'ERROR', 'CRITICAL' => 'CRITICAL', 'WARNING' => 'WARNING', 'NOTICE' => 'NOTICE', 'INFO' => 'INFO', 'DEBUG' => 'DEBUG'];
    @endphp

    {{-- ファイル情報 + フィルター --}}
    <x-filament::section>
        <div class="flex flex-wrap items-center gap-4">
            {{-- ファイル情報 --}}
            @if ($fileInfo['exists'])
                <div class="text-xs text-gray-500 dark:text-gray-400 shrink-0">
                    <span class="font-mono">laravel.log</span>
                    &nbsp;{{ $fileInfo['size'] }}
                    &nbsp;·&nbsp;最終更新: {{ $fileInfo['modified'] }}
                </div>
            @else
                <div class="text-xs text-gray-400">ログファイルなし</div>
            @endif

            <div class="flex items-center gap-3 ml-auto flex-wrap">
                {{-- レベルフィルター --}}
                <select
                    wire:model.live="filterLevel"
                    class="rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-sm text-gray-700 dark:text-gray-200 px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    @foreach ($levelLabels as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                    @endforeach
                </select>

                {{-- キーワード検索 --}}
                <input
                    wire:model.live.debounce.400ms="filterKeyword"
                    type="text"
                    placeholder="キーワード検索"
                    class="rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 text-sm text-gray-700 dark:text-gray-200 px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500 w-48"
                />

                <span class="text-xs text-gray-400">{{ count($entries) }} 件</span>
            </div>
        </div>
    </x-filament::section>

    {{-- ログ一覧 --}}
    @if (empty($entries))
        <x-filament::section>
            <div class="text-sm text-gray-400 text-center py-4">
                {{ $fileInfo['exists'] ? '該当するログがありません' : 'ログファイルが存在しません' }}
            </div>
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="divide-y divide-gray-100 dark:divide-white/5 -mx-6 -my-4">
                @foreach ($entries as $entry)
                    @php $color = \App\Filament\Pages\OprErrorLogPage::levelColor($entry['level']); @endphp
                    <div
                        x-data="{ open: false }"
                        class="px-6 py-3"
                    >
                        <div class="flex items-start gap-3">
                            {{-- レベルバッジ --}}
                            <span @class([
                                'shrink-0 inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold w-20 justify-center mt-0.5',
                                'bg-danger-100 dark:bg-danger-500/20 text-danger-700 dark:text-danger-400'   => $color === 'danger',
                                'bg-warning-100 dark:bg-warning-500/20 text-warning-700 dark:text-warning-400' => $color === 'warning',
                                'bg-info-100 dark:bg-info-500/20 text-info-700 dark:text-info-400'           => $color === 'info',
                                'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-400'              => $color === 'gray',
                            ])>
                                {{ $entry['level'] }}
                            </span>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-mono text-gray-400 shrink-0">{{ $entry['at'] }}</span>
                                    @if ($entry['env'] !== 'local')
                                        <span class="text-xs text-gray-400">[{{ $entry['env'] }}]</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-800 dark:text-gray-200 mt-0.5 break-all">
                                    {{ $entry['message'] }}
                                </p>
                            </div>

                            {{-- 詳細トグル --}}
                            @if ($entry['detail'])
                                <button
                                    @click="open = !open"
                                    type="button"
                                    class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 mt-0.5"
                                    :title="open ? '閉じる' : '詳細を表示'"
                                >
                                    <span :class="open ? 'rotate-180' : ''" class="transition-transform inline-flex">
                                        <x-heroicon-m-chevron-down class="w-4 h-4" />
                                    </span>
                                </button>
                            @endif
                        </div>

                        {{-- スタックトレース --}}
                        @if ($entry['detail'])
                            <div x-show="open" x-cloak class="mt-2 ml-23">
                                <pre class="text-xs font-mono text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-white/5 rounded-lg p-3 overflow-x-auto whitespace-pre-wrap break-all">{{ $entry['detail'] }}</pre>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
