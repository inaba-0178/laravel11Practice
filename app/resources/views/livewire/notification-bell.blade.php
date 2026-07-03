<div
    x-data="{
        open: false,
        top: 0,
        right: 0,
        toggle() {
            const rect = this.$refs.bell.getBoundingClientRect();
            this.top   = rect.bottom + 8;
            this.right = window.innerWidth - rect.right;
            this.open  = !this.open;
        }
    }"
    x-on:click.outside="open = false"
    wire:poll.30s="loadUnreadCount"
    class="relative flex items-center"
>
    {{-- ベルボタン --}}
    <div
        class="relative flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 text-white hover:bg-gray-600 transition-colors"
        x-ref="bell"
        @click="toggle()"
        style="cursor:pointer; flex-shrink:0;"
    >
        <x-heroicon-o-bell class="w-5 h-5" />

        @if ($unreadCount > 0)
            <span
                class="absolute flex items-center justify-center rounded-full text-white font-bold"
                style="top:1px; right:1px; min-width:18px; height:18px; background-color:#ef4444; font-size:10px; line-height:1; padding:0 3px; border:2px solid #374151; z-index:10;"
            >{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </div>

    {{-- ドロップダウン（bodyにテレポート） --}}
    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            :style="`position: fixed; top: ${top}px; right: ${right}px; transform-origin: top right;`"
            class="w-72 rounded-2xl bg-gray-900 shadow-xl ring-1 ring-white/10 z-[9999] overflow-hidden"
            @click.outside="open = false"
        >
            {{-- ヘッダー --}}
            <div class="px-4 pt-4 pb-2">
                <span class="text-sm font-semibold text-white">通知</span>
            </div>

            {{-- コンテンツ --}}
            @if ($unreadCount > 0)
                <a
                    href="{{ $this->getChatListUrl() }}"
                    @click="open = false"
                    class="flex items-center gap-3 mx-3 mb-3 px-3 py-3 rounded-xl hover:bg-white/5 transition-colors"
                >
                    <div class="shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-indigo-500/20">
                        <x-heroicon-s-chat-bubble-left-ellipsis class="w-5 h-5 text-indigo-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-white leading-snug">未読メッセージ</p>
                        <p class="text-xs text-pink-400 font-medium mt-0.5">{{ $unreadCount }}件の未読</p>
                    </div>
                </a>
            @else
                <div class="px-4 pb-4 text-sm text-gray-500 text-center py-3">
                    通知はありません
                </div>
            @endif
        </div>
    </template>
</div>
