<x-filament-panels::page>
    @php
        $participants  = $this->getParticipants();
        $currentUserId = (string) auth()->id();
        $canEdit       = in_array(auth()->user()?->role, ['dealer', 'dealer_staff']);
        $isAdmin       = in_array(auth()->user()?->role, ['super', 'admin']);
        $canViewLog    = in_array(auth()->user()?->role, ['dealer', 'dealer_staff', 'super', 'admin']);
        $deletionLogs  = $this->getDeletionLogs();
    @endphp

    {{-- 参加者情報 --}}
    <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 14px 18px; margin-bottom: 16px;">
        <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0 0 10px; padding-left: 10px; border-left: 3px solid #185FA5;">参加者</p>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            @foreach($participants as $participant)
            @php
                $badgeColor = match(true) {
                    $participant['role'] === 'super'      => '#d97706',
                    $participant['role'] === 'admin'      => '#dc2626',
                    $participant['user_type'] === 'staff' => '#185FA5',
                    default                               => '#dc5078',
                };
                $statusLabel = match($participant['status']) {
                    'pending'  => '招待中',
                    'rejected' => '拒否',
                    default    => null,
                };
            @endphp
            <div style="background: #f9fafb; border: 0.5px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 10px;">
                <div style="background: {{ $badgeColor }}; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 500; flex-shrink: 0;">
                    {{ mb_substr($participant['name'], 0, 1) }}
                </div>
                <div>
                    <p style="font-size: 13px; font-weight: 500; color: #111827; margin: 0;">
                        {{ $participant['name'] }}
                        @if($statusLabel)
                        <span style="background: #fef3c7; color: #92400e; padding: 1px 6px; border-radius: 4px; font-size: 10px; margin-left: 4px;">{{ $statusLabel }}</span>
                        @endif
                    </p>
                    <p style="font-size: 11px; color: #6b7280; margin: 2px 0 0;">
                        <span style="background: {{ $participant['user_type'] === 'staff' ? '#eff6ff' : '#fdf2f8' }}; color: {{ $participant['user_type'] === 'staff' ? '#1d4ed8' : '#be185d' }}; padding: 1px 6px; border-radius: 4px; font-size: 10px;">{{ $participant['type_label'] }}</span>
                        　{{ $participant['email'] }}
                    </p>
                </div>
                @if($canEdit && $participant['id'] !== $currentUserId)
                <button
                    wire:click="removeParticipant({{ $participant['room_user_id'] }})"
                    style="margin-left: auto; background: #fee2e2; color: #dc2626; border: none; border-radius: 6px; padding: 4px 8px; font-size: 11px; cursor: pointer;"
                >
                    削除
                </button>
                @endif
            </div>
            @endforeach
        </div>

        {{-- 削除された参加者ボタン --}}
        @if($canViewLog && count($deletionLogs) > 0)
        <div style="margin-top: 12px;">
            <button
                wire:click="$set('showDeletionLogs', true)"
                style="background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; border-radius: 6px; padding: 6px 12px; font-size: 12px; cursor: pointer;"
            >
                🗑 削除された参加者（{{ count($deletionLogs) }}件）
            </button>
        </div>
        @endif
    </div>

    {{-- 削除確認モーダル --}}
    @if($this->deletingRoomUserId)
    <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 99999; display: flex; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 24px; width: 400px;">
            <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 16px; color: #111827;">参加者を削除</h3>
            <div style="margin-bottom: 12px;">
                <label style="font-size: 12px; color: #374151; display: block; margin-bottom: 4px;">削除理由 <span style="color: #dc2626;">*</span></label>
                <select wire:model="deleteReason" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; color: #111827;">
                    <option value="">選択してください</option>
                    <option value="担当者交代">担当者交代</option>
                    <option value="クレーム対応">クレーム対応</option>
                    <option value="誤追加">誤追加</option>
                    <option value="その他">その他</option>
                </select>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; color: #374151; display: block; margin-bottom: 4px;">詳細（任意）</label>
                <textarea
                    wire:model="deleteReasonDetail"
                    rows="3"
                    style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; color: #111827; resize: none; box-sizing: border-box;"
                    placeholder="詳細を入力..."
                ></textarea>
            </div>
            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                <button wire:click="cancelDelete" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; cursor: pointer; font-size: 13px;">キャンセル</button>
                <button wire:click="confirmDelete" style="padding: 8px 16px; background: #dc2626; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 13px;">削除する</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 削除ログモーダル --}}
    @if($this->showDeletionLogs)
    <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 99999; display: flex; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 24px; width: 500px; max-height: 80vh; overflow-y: auto;">
            <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 16px; color: #111827;">削除された参加者</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($deletionLogs as $log)
                <div style="background: #fef2f2; border: 0.5px solid #fecaca; border-radius: 8px; padding: 12px 14px; font-size: 12px; color: #374151;">
                    <p style="margin: 0 0 4px;"><strong>削除された人：</strong>{{ $log['deleted_user_name'] }}</p>
                    <p style="margin: 0 0 4px;"><strong>削除日時：</strong>{{ $log['deleted_at'] }}</p>
                    <p style="margin: 0 0 4px;"><strong>削除理由：</strong>{{ $log['reason'] }}</p>
                    @if($log['reason_detail'])
                    <p style="margin: 0 0 4px;"><strong>詳細：</strong>{{ $log['reason_detail'] }}</p>
                    @endif
                    <p style="margin: 0;"><strong>削除した人：</strong>{{ $log['deleted_by_name'] }}</p>
                </div>
                @endforeach
            </div>
            <div style="margin-top: 16px; text-align: right;">
                <button wire:click="$set('showDeletionLogs', false)" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; cursor: pointer; font-size: 13px;">閉じる</button>
            </div>
        </div>
    </div>
    @endif
    {{-- Lightbox --}}
        <div id="lightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.9); z-index: 99999; align-items: center; justify-content: center;" onclick="closeLightbox()">
            <img id="lightbox-image" src="" style="max-width: 90vw; max-height: 90vh; object-fit: contain; border-radius: 4px;" />
        </div>

    {{-- メッセージエリア --}}
    <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; overflow: hidden;">

        {{-- メッセージ一覧 --}}
        <div
            style="height: 500px; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 12px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;"
            id="message-list"
        >
            @php $lastDate = null; @endphp
            @forelse($messages as $message)

            {{-- 日付区切り --}}
            @if($message['date'] !== $lastDate)
            <div style="display: flex; align-items: center; gap: 8px; margin: 8px 0;">
                <div style="flex: 1; height: 1px; background: #d1d5db;"></div>
                <span style="font-size: 11px; color: #9ca3af; white-space: nowrap; padding: 0 8px;">{{ $message['date'] }}</span>
                <div style="flex: 1; height: 1px; background: #d1d5db;"></div>
            </div>
            @php $lastDate = $message['date']; @endphp
            @endif

            @php
                $msgBadgeColor = match(true) {
                    $message['role'] === 'super'      => '#d97706',
                    $message['role'] === 'admin'      => '#dc2626',
                    $message['user_type'] === 'staff' => '#185FA5',
                    default                           => '#dc5078',
                };
            @endphp
            <div style="display: flex; gap: 8px; max-width: 70%; {{ $message['is_mine'] ? 'align-self: flex-end;' : 'align-self: flex-start;' }}">
                @if(!$message['is_mine'])
                <div style="background: {{ $msgBadgeColor }}; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 500; flex-shrink: 0; align-self: flex-start; margin-top: 20px;">
                    {{ mb_substr($message['user_name'], 0, 1) }}
                </div>
                @endif
                <div style="display: flex; flex-direction: column; {{ $message['is_mine'] ? 'align-items: flex-end;' : 'align-items: flex-start;' }}">
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">{{ $message['user_name'] }}</div>

                    {{-- テキスト --}}
                    @if($message['message'])
                    <div style="padding: 8px 12px; border-radius: 12px; font-size: 13px; white-space: pre-wrap; word-break: break-word; display: inline-block; text-align: left; max-width: 100%; {{ $message['is_mine'] ? 'background: #185FA5; color: white;' : 'background: #e5e7eb; color: #111827;' }}">{{ $message['message'] }}</div>
                    @endif

                    {{-- 画像 --}}
                    @if(($message['attachment_type'] ?? '') === 'image')
                    <div style="margin-top: 4px;">
                        <img
                            src="{{ $message['attachment_url'] }}"
                            alt="{{ $message['attachment_name'] }}"
                            style="max-width: 200px; max-height: 200px; border-radius: 8px; object-fit: cover; display: block; cursor: pointer;"
                            onclick="openLightbox('{{ $message['attachment_url'] }}')"
                        />
                    </div>

                    {{-- 動画 --}}
                    @elseif(($message['attachment_type'] ?? '') === 'video')
                    <div style="margin-top: 4px;">
                        <video
                            src="{{ $message['attachment_url'] }}"
                            style="max-width: 280px; border-radius: 8px; display: block;"
                            controls
                            preload="metadata"
                        ></video>
                    </div>

                    {{-- ファイル --}}
                    @elseif(($message['attachment_type'] ?? '') === 'file')
                        <div style="margin-top: 4px;">
                            <a
                                href="{{ $message['attachment_url'] }}"
                                target="_blank"
                                style="display: flex; align-items: center; gap: 10px; background: {{ $message['is_mine'] ? 'rgba(255,255,255,0.15)' : 'white' }}; border: 0.5px solid {{ $message['is_mine'] ? 'rgba(255,255,255,0.3)' : '#e5e7eb' }}; border-radius: 10px; padding: 10px 14px; text-decoration: none; width: 280px;"
                            >
                                <div style="width: 36px; height: 36px; background: #f0f4ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#185FA5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                </div>
                                <div style="display: flex; flex-direction: column; min-width: 0; flex: 1;">
                                    <span style="font-size: 12px; font-weight: 500; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;">{{ $message['attachment_name'] }}</span>
                                    <span style="font-size: 10px; color: #111827; margin-top: 2px;">
                                        @php
                                            $size = $message['attachment_size'] ?? 0;
                                            echo $size < 1024 ? "{$size}B" : ($size < 1048576 ? round($size/1024, 1).'KB' : round($size/1048576, 1).'MB');
                                        @endphp
                                    </span>
                                </div>
                            </a>
                        </div>
                    @endif

                    <div style="display: flex; gap: 8px; font-size: 11px; color: #9ca3af; margin-top: 4px;">
                        <span>{{ $message['created_at'] }}</span>
                        @if($message['is_mine'])
                        <span>{{ $message['read_count'] > 0 ? '既読 ' . $message['read_count'] : '未読' }}</span>
                        @endif
                    </div>
                </div>
                @if($message['is_mine'])
                <div style="background: {{ $msgBadgeColor }}; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 500; flex-shrink: 0; align-self: flex-start; margin-top: 20px;">
                    {{ mb_substr($message['user_name'], 0, 1) }}
                </div>
                @endif
            </div>

            @empty
            <p style="text-align: center; color: #9ca3af; font-size: 13px;">メッセージがありません</p>
            @endforelse
        </div>

        {{-- 入力欄 --}}
        @php $canSend = !in_array(auth()->user()?->role, ['super', 'admin']) || auth()->user()?->dealer_id; @endphp
        @if($canSend)
        <div
            x-data="chatAttachment({{ $this->record->id }})"
            style="padding: 12px 16px; border-top: 0.5px solid #e5e7eb;"
        >
            {{-- 添付ファイルプレビュー --}}
            <div x-show="previews.length > 0" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 8px;">
                <template x-for="(preview, index) in previews" :key="index">
                    <div style="position: relative; border-radius: 8px; overflow: hidden; border: 1px solid #d1d5db;">
                        <img x-show="preview.type === 'image'" :src="preview.url" style="width: 80px; height: 80px; object-fit: cover; display: block;" />
                        <div x-show="preview.type === 'video'" style="width: 80px; height: 80px; background: #1a1a1a; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">▶</div>
                        <div x-show="preview.type === 'file'" style="width: 80px; height: 80px; background: #f5f5f5; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 8px;">
                            <span style="font-size: 24px;">📄</span>
                            <span x-text="preview.name" style="font-size: 10px; color: #666; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%;"></span>
                        </div>
                        <button @click="removeFile(index)" style="position: absolute; top: 2px; right: 2px; width: 18px; height: 18px; border-radius: 50%; background: rgba(0,0,0,0.6); color: white; border: none; cursor: pointer; font-size: 12px; display: flex; align-items: center; justify-content: center;">×</button>
                    </div>
                </template>
            </div>

            {{-- 入力行 --}}
            <div style="display: flex; gap: 8px; align-items: flex-end;">
                <label style="cursor: pointer; color: #9ca3af; display: flex; align-items: center; padding: 8px; border-radius: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                    </svg>
                    <input type="file" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.pdf,.doc,.docx,.xls,.xlsx" @change="handleFileChange" style="display: none;" x-ref="fileInput" />
                </label>

                <textarea
                    wire:model="newMessage"
                    @keydown.enter.exact.prevent="handleSendWithAttachment"
                    placeholder="メッセージを入力... (Enterで送信)"
                    rows="2"
                    style="flex: 1; resize: none; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; color: #111827; background: #ffffff;"
                ></textarea>

                <button @click="handleSendWithAttachment" :disabled="sending" style="padding: 8px 20px; background: #185FA5; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; height: 40px;">
                    <span x-text="sending ? '送信中...' : '送信'"></span>
                </button>
            </div>

            <p x-show="error" x-text="error" style="font-size: 12px; color: #dc2626; margin: 4px 0 0;"></p>
        </div>
        @else
        <div style="padding: 12px 16px; border-top: 0.5px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 13px;">
            閲覧のみ（送信権限がありません）
        </div>
        @endif
    </div>

@script
<script>
    // グローバルに登録
    window.chatAttachment = function(roomId) {
        return {
            files:    [],
            previews: [],
            sending:  false,
            error:    '',

            handleFileChange(e) {
                this.error = ''
                const newFiles = Array.from(e.target.files)

                if (this.files.length + newFiles.length > 5) {
                    this.error = '一度に送信できるファイルは5件までです'
                    return
                }

                const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp']
                const videoTypes = ['mp4', 'mov']
                const maxSizes   = { image: 10, video: 100, file: 20 }

                for (const file of newFiles) {
                    const ext  = file.name.split('.').pop().toLowerCase()
                    const type = imageTypes.includes(ext) ? 'image' : videoTypes.includes(ext) ? 'video' : 'file'
                    const max  = maxSizes[type] * 1024 * 1024

                    if (file.size > max) {
                        this.error = `${file.name} のサイズが上限（${maxSizes[type]}MB）を超えています`
                        return
                    }

                    this.files.push(file)
                    this.previews.push({
                        name: file.name,
                        type: type,
                        url:  type === 'image' ? URL.createObjectURL(file) : null,
                    })
                }

                this.$refs.fileInput.value = ''
            },

            removeFile(index) {
                this.files.splice(index, 1)
                this.previews.splice(index, 1)
            },

            async handleSendWithAttachment() {
                if (this.sending) return
                this.sending = true
                this.error   = ''

                try {
                    if (this.files.length > 0) {
                        const formData = new FormData()
                        this.files.forEach((file, i) => formData.append(`files[${i}]`, file))

                        const response = await fetch(`/api/rooms/${roomId}/attachments`, {
                            method:  'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        })

                        if (!response.ok) {
                            const data = await response.json()
                            this.error = data.message ?? 'アップロードに失敗しました'
                            return
                        }

                        const attachments = await response.json()

                        for (const attachment of attachments) {
                            await $wire.sendMessageWithAttachment(attachment)
                        }

                        this.files    = []
                        this.previews = []
                    } else {
                        await $wire.sendMessage()
                    }
                } catch (e) {
                    this.error = 'エラーが発生しました'
                } finally {
                    this.sending = false
                }
            }
        }
    }
    window.openLightbox = function(url) {
        const lb = document.getElementById('lightbox')
        const img = document.getElementById('lightbox-image')
        img.src = url
        lb.style.display = 'flex'
    }

    window.closeLightbox = function() {
        document.getElementById('lightbox').style.display = 'none'
    }

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