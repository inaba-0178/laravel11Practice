<x-filament-panels::page>
    @php
        $record    = $this->record;
        $isPending = !empty($record->loan_setting_requested_at)
            && $record->loan_setting_enabled == 0
            && empty($record->loan_setting_rejected_reason);
        $isApproved = $record->loan_setting_enabled == 1;
        $isRejected = !empty($record->loan_setting_rejected_reason);
    @endphp

    <div style="display: flex; flex-direction: column; gap: 16px; max-width: 800px;">

        {{-- ステータスバー --}}
        <div style="background: {{ $isApproved ? '#f0fdf4' : ($isRejected ? '#fef2f2' : '#fef3c7') }}; border: 1px solid {{ $isApproved ? '#86efac' : ($isRejected ? '#fca5a5' : '#fcd34d') }}; border-radius: 10px; padding: 14px 20px; display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 22px;">{{ $isApproved ? '✅' : ($isRejected ? '❌' : '⏳') }}</span>
            <div>
                <p style="font-size: 14px; font-weight: 500; color: {{ $isApproved ? '#15803d' : ($isRejected ? '#991b1b' : '#92400e') }}; margin: 0;">
                    {{ $isApproved ? '許可済み' : ($isRejected ? '拒否済み' : '申請中') }}
                </p>
                @if($isApproved)
                    <p style="font-size: 12px; color: #15803d; margin: 3px 0 0;">
                        {{ $record->loan_setting_approved_at?->format('Y/m/d H:i') }} に承認
                    </p>
                @elseif($isRejected)
                    <p style="font-size: 12px; color: #991b1b; margin: 3px 0 0;">
                        拒否理由：{{ $record->loan_setting_rejected_reason }}
                    </p>
                @endif
            </div>
        </div>

        {{-- 申請内容 --}}
        <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; overflow: hidden;">
            <div style="padding: 12px 18px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;">
                <p style="font-size: 13px; font-weight: 500; color: #374151; margin: 0;">申請内容</p>
            </div>
            <div style="padding: 16px 18px; display: flex; flex-direction: column; gap: 12px;">

                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 8px; align-items: start;">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">ディーラー名</p>
                    <p style="font-size: 13px; color: #111827; margin: 0; font-weight: 500;">{{ $record->name }}</p>
                </div>

                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 8px; align-items: start;">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">申請担当者</p>
                    <p style="font-size: 13px; color: #111827; margin: 0;">
                        {{ $record->requestedBy?->name ?? '-' }}
                    </p>
                </div>

                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 8px; align-items: start;">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">申請日時</p>
                    <p style="font-size: 13px; color: #111827; margin: 0;">
                        {{ $record->loan_setting_requested_at?->format('Y/m/d H:i') ?? '-' }}
                    </p>
                </div>

                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 8px; align-items: start;">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">申請理由</p>
                    <p style="font-size: 13px; color: #111827; margin: 0; line-height: 1.6;">
                        {{ $record->loan_setting_reason ?? '-' }}
                    </p>
                </div>

                @if($isApproved || $isRejected)
                <div style="border-top: 0.5px solid #e5e7eb; padding-top: 12px; display: grid; grid-template-columns: 140px 1fr; gap: 8px; align-items: start;">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">対応した管理者</p>
                    <p style="font-size: 13px; color: #111827; margin: 0;">
                        {{ $record->approvedBy?->name ?? '-' }}
                    </p>
                </div>
                <div style="display: grid; grid-template-columns: 140px 1fr; gap: 8px; align-items: start;">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">対応日時</p>
                    <p style="font-size: 13px; color: #111827; margin: 0;">
                        {{ $record->loan_setting_approved_at?->format('Y/m/d H:i') ?? '-' }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- アクションボタン --}}
        @if($isPending)
        <div style="display: flex; flex-direction: column; gap: 10px;">
            {{-- 許可ボタン --}}
            <button
                type="button"
                wire:click="approve"
                wire:confirm="このディーラーのローン設定を許可しますか？"
                style="width: 100%; padding: 13px; background: #3B6D11; color: #EAF3DE; border: none; border-radius: 10px; font-size: 14px; font-weight: 500; cursor: pointer;"
            >
                許可する
            </button>

            {{-- 拒否パネル --}}
            <div
                x-data="{ showReject: false, reason: '' }"
            >
                <button
                    type="button"
                    x-on:click="showReject = !showReject"
                    style="width: 100%; padding: 13px; background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; border-radius: 10px; font-size: 14px; font-weight: 500; cursor: pointer;"
                >
                    拒否する
                </button>

                <div x-show="showReject" style="margin-top: 10px; background: white; border-radius: 10px; border: 0.5px solid #fca5a5; padding: 14px;">
                    <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 6px;">拒否理由を入力 <span style="color: #dc2626;">*</span></label>
                    <textarea
                        x-model="reason"
                        style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 12px; color: #111827; background: #f9fafb; resize: vertical; min-height: 80px;"
                        placeholder="拒否する理由を入力してください"
                    ></textarea>
                    <button
                        type="button"
                        x-on:click="
                            if (!reason.trim()) { alert('拒否理由を入力してください'); return; }
                            $wire.reject(reason);
                        "
                        style="margin-top: 10px; width: 100%; padding: 10px; background: #E24B4A; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
                    >
                        拒否を確定する
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- 許可取り消しボタン --}}
        @if($isApproved)
        <button
            type="button"
            wire:click="revoke"
            wire:confirm="ローン設定の許可を取り消しますか？ディーラーはローン設定ができなくなります。"
            style="width: 100%; padding: 13px; background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; border-radius: 10px; font-size: 14px; font-weight: 500; cursor: pointer;"
        >
            許可を取り消す
        </button>
        @endif
    </div>
</x-filament-panels::page>