<x-filament-panels::page>
    @php
        $participants = $this->getParticipants();
        $currentUserId = (string) auth()->id();
    @endphp

    {{-- 参加者情報 --}}
    <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 14px 18px; margin-bottom: 16px;">
        <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0 0 10px; padding-left: 10px; border-left: 3px solid #185FA5;">参加者</p>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            @foreach($participants as $participant)
            <div style="background: #f9fafb; border: 0.5px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 10px;">
                <div style="background: {{ $participant['user_type'] === 'staff' ? '#185FA5' : '#dc5078' }}; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 500; flex-shrink: 0;">
                    {{ mb_substr($participant['name'], 0, 1) }}
                </div>
                <div>
                    <p style="font-size: 13px; font-weight: 500; color: #111827; margin: 0;">{{ $participant['name'] }}</p>
                    <p style="font-size: 11px; color: #6b7280; margin: 2px 0 0;">
                        <span style="background: {{ $participant['user_type'] === 'staff' ? '#eff6ff' : '#fdf2f8' }}; color: {{ $participant['user_type'] === 'staff' ? '#1d4ed8' : '#be185d' }}; padding: 1px 6px; border-radius: 4px; font-size: 10px;">{{ $participant['type_label'] }}</span>
                        　{{ $participant['email'] }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- メッセージエリア --}}
    <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; overflow: hidden;">

        {{-- メッセージ一覧 --}}
        <div
            style="height: 500px; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 12px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;"
            id="message-list"
        >
            @forelse($messages as $message)
            <div style="display: flex; flex-direction: column; max-width: 60%; {{ $message['is_mine'] ? 'align-self: flex-end; align-items: flex-end;' : 'align-self: flex-start; align-items: flex-start;' }}">
                @if(!$message['is_mine'])
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">{{ $message['user_name'] }}</div>
                @endif
                <div style="padding: 8px 12px; border-radius: 12px; font-size: 13px; {{ $message['is_mine'] ? 'background: #185FA5; color: white;' : 'background: #e5e7eb; color: #111827;' }}">
                    {{ $message['message'] }}
                </div>
                <div style="display: flex; gap: 8px; font-size: 11px; color: #9ca3af; margin-top: 4px;">
                    <span>{{ $message['created_at'] }}</span>
                    @if($message['is_mine'])
                    <span>{{ $message['read_count'] > 0 ? '既読 ' . $message['read_count'] : '未読' }}</span>
                    @endif
                </div>
            </div>
            @empty
            <p style="text-align: center; color: #9ca3af; font-size: 13px;">メッセージがありません</p>
            @endforelse
        </div>

        {{-- 入力欄 --}}
        <div style="padding: 12px 16px; border-top: 0.5px solid #e5e7eb; display: flex; gap: 8px; align-items: flex-end;">
            <textarea
                wire:model="newMessage"
                wire:keydown.enter.exact.prevent="sendMessage"
                placeholder="メッセージを入力... (Enterで送信)"
                rows="2"
                style="flex: 1; resize: none; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; color: #111827; background: #ffffff;"
            ></textarea>
            <button
                wire:click="sendMessage"
                style="padding: 8px 20px; background: #185FA5; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; height: 40px;"
            >
                送信
            </button>
        </div>
    </div>

    {{-- 自動スクロール --}}
    {{-- @script @endscript を削除して以下に置き換え --}}

<div
    x-data="{}"
    x-init="
        $nextTick(() => {
            document.getElementById('message-list').scrollTop = document.getElementById('message-list').scrollHeight;
        })
    "
></div>

@script
<script>
    Echo.channel('room.{{ $this->record->id }}')
        .listen('.message.sent', function(e) {
            $wire.onMessageReceived();
        });

    function scrollToBottom() {
        var el = document.getElementById('message-list');
        if (el) el.scrollTop = el.scrollHeight;
    }
    scrollToBottom();

    $wire.on('messages-updated', function() {
        setTimeout(scrollToBottom, 50);
    });
</script>
@endscript

</x-filament-panels::page>