<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 16px;">

        {{-- 現在の状態 --}}
        <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 24px;">
            <p style="font-size: 14px; font-weight: 500; color: #374151; margin: 0 0 16px;">現在の状態</p>
            <div style="display: flex; align-items: center; gap: 12px; padding: 16px; border-radius: 10px;
                {{ $isMaintenance ? 'background: #fef3c7; border: 0.5px solid #fcd34d;' : 'background: #f0fdf4; border: 0.5px solid #bbf7d0;' }}">
                <span style="font-size: 24px;">{{ $isMaintenance ? '🔧' : '✅' }}</span>
                <div>
                    <p style="font-size: 15px; font-weight: 500; margin: 0;
                        {{ $isMaintenance ? 'color: #92400e;' : 'color: #15803d;' }}">
                        {{ $isMaintenance ? 'メンテナンス中' : '通常稼働中' }}
                    </p>
                    <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0;">
                        {{ $isMaintenance ? '一般ユーザーはサイトにアクセスできません' : '一般ユーザーは通常通りアクセスできます' }}
                    </p>
                    @if($startedAt && !$isMaintenance)
                    <p style="font-size: 12px; color: #d97706; margin: 4px 0 0;">
                        📅 開始予定：{{ $startedAt }}　終了予定：{{ $estimatedEndAt }}
                    </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- モード切り替え --}}
        @if(!$isMaintenance)
        <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 24px;">
            <p style="font-size: 14px; font-weight: 500; color: #374151; margin: 0 0 16px;">モード選択</p>
            <div style="display: flex; gap: 8px;">
                <button
                    type="button"
                    wire:click="$set('mode', 'manual')"
                    style="padding: 8px 20px; border-radius: 8px; font-size: 13px; cursor: pointer;
                        {{ $mode === 'manual'
                            ? 'background: #185FA5; color: white; border: none;'
                            : 'background: #f3f4f6; color: #374151; border: 0.5px solid #e5e7eb;'
                        }}"
                >
                    手動モード
                </button>
                <button
                    type="button"
                    wire:click="$set('mode', 'schedule')"
                    style="padding: 8px 20px; border-radius: 8px; font-size: 13px; cursor: pointer;
                        {{ $mode === 'schedule'
                            ? 'background: #185FA5; color: white; border: none;'
                            : 'background: #f3f4f6; color: #374151; border: 0.5px solid #e5e7eb;'
                        }}"
                >
                    スケジュールモード
                </button>
            </div>
        </div>

        {{-- 手動モード --}}
        @if($mode === 'manual')
        <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 24px;">
            <p style="font-size: 14px; font-weight: 500; color: #374151; margin: 0 0 16px;">手動メンテナンス設定</p>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div>
                    <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">メンテナンスメッセージ</label>
                    <textarea
                        wire:model="message"
                        rows="3"
                        placeholder="例：システムメンテナンスのため一時的にサービスを停止しています"
                        style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 13px; color: #111827; background: #f9fafb; resize: vertical;"
                    ></textarea>
                </div>
                <button
                    type="button"
                    wire:click="enableMaintenance"
                    wire:confirm="メンテナンスモードをONにしますか？一般ユーザーがサイトにアクセスできなくなります。"
                    style="padding: 10px 20px; background: #d97706; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; align-self: flex-start;"
                >
                    今すぐメンテナンスON
                </button>
            </div>
        </div>

        {{-- スケジュールモード --}}
        @else
        <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 24px;">
            <p style="font-size: 14px; font-weight: 500; color: #374151; margin: 0 0 16px;">スケジュール設定</p>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div>
                    <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">メンテナンスメッセージ</label>
                    <textarea
                        wire:model="message"
                        rows="3"
                        placeholder="例：システムメンテナンスのため一時的にサービスを停止しています"
                        style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 13px; color: #111827; background: #f9fafb; resize: vertical;"
                    ></textarea>
                </div>
                <div>
                    <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">開始予定日時 <span style="color: #dc2626;">*</span></label>
                    <input
                        type="datetime-local"
                        wire:model="startedAt"
                        style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 13px; color: #111827; background: #f9fafb;"
                    />
                </div>
                <div>
                    <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">終了予定日時 <span style="color: #dc2626;">*</span></label>
                    <input
                        type="datetime-local"
                        wire:model="estimatedEndAt"
                        style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 13px; color: #111827; background: #f9fafb;"
                    />
                </div>
                <div style="display: flex; gap: 8px;">
                    <button
                        type="button"
                        wire:click="scheduleMainenance"
                        wire:confirm="スケジュールを設定しますか？指定時間になると自動でメンテナンスが開始されます。"
                        style="padding: 10px 20px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
                    >
                        スケジュール設定
                    </button>
                    @if($startedAt)
                    <button
                        type="button"
                        wire:click="cancelSchedule"
                        wire:confirm="スケジュールをキャンセルしますか？"
                        style="padding: 10px 20px; background: #f3f4f6; color: #374151; border: 0.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
                    >
                        キャンセル
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- メンテナンス解除 --}}
        @else
        <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 24px;">
            <p style="font-size: 14px; font-weight: 500; color: #374151; margin: 0 0 16px;">メンテナンス解除</p>
            <p style="font-size: 13px; color: #6b7280; margin: 0 0 16px;">
                メンテナンスを終了すると一般ユーザーが通常通りアクセスできるようになります。
            </p>
            <button
                type="button"
                wire:click="disableMaintenance"
                wire:confirm="メンテナンスモードをOFFにしますか？"
                style="padding: 10px 20px; background: #3B6D11; color: #EAF3DE; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
            >
                メンテナンスOFFにする
            </button>
        </div>
        @endif
    </div>
</x-filament-panels::page>