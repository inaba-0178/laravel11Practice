<x-filament-panels::page>
    @php
        $dealer     = $this->dealer;
        $isPending  = $dealer && !empty($dealer->loan_setting_requested_at)
            && $dealer->loan_setting_enabled == 0
            && empty($dealer->loan_setting_rejected_reason);
        $isApproved = $dealer?->loan_setting_enabled == 1;
        $isRejected = !empty($dealer?->loan_setting_rejected_reason);
        $isNotApplied = !$isPending && !$isApproved && !$isRejected;
    @endphp

    <div style="max-width: 640px; display: flex; flex-direction: column; gap: 16px;">

        {{-- ===== 承認済み ===== --}}
        @if($isApproved)
        <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 24px;">✅</span>
            <div>
                <p style="font-size: 14px; font-weight: 500; color: #15803d; margin: 0;">ローン設定が許可されています</p>
                <p style="font-size: 12px; color: #15803d; margin: 3px 0 0; opacity: 0.8;">
                    承認日時：{{ $dealer->loan_setting_approved_at?->format('Y/m/d H:i') }}
                </p>
            </div>
        </div>

        <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; padding: 16px 20px;">
            <p style="font-size: 13px; color: #374151; margin: 0 0 12px;">ローンプランの管理はこちらから行えます。</p>
            <a href="{{ \App\Filament\Resources\DealerLoanResource::getUrl('index') }}"
               style="display: inline-block; padding: 10px 20px; background: #185FA5; color: white; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;">
                ローンプラン管理へ
            </a>
        </div>

        {{-- ===== 申請中 ===== --}}
        @elseif($isPending)
        <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 24px;">⏳</span>
            <div>
                <p style="font-size: 14px; font-weight: 500; color: #92400e; margin: 0;">申請中です</p>
                <p style="font-size: 12px; color: #92400e; margin: 3px 0 0; opacity: 0.8;">
                    管理者が確認中です。承認までしばらくお待ちください。
                </p>
            </div>
        </div>

        <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; overflow: hidden;">
            <div style="padding: 10px 16px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;">
                <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0;">申請内容</p>
            </div>
            <div style="padding: 14px 16px; display: flex; flex-direction: column; gap: 10px;">
                <div style="display: grid; grid-template-columns: 120px 1fr; gap: 8px;">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">申請日時</p>
                    <p style="font-size: 13px; color: #111827; margin: 0;">{{ $dealer->loan_setting_requested_at?->format('Y/m/d H:i') }}</p>
                </div>
                <div style="display: grid; grid-template-columns: 120px 1fr; gap: 8px;">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">申請理由</p>
                    <p style="font-size: 13px; color: #111827; margin: 0; line-height: 1.6;">{{ $dealer->loan_setting_reason }}</p>
                </div>
            </div>
        </div>

        <button
            type="button"
            wire:click="cancelRequest"
            wire:confirm="申請を取り消しますか？"
            style="padding: 10px 20px; background: #fef2f2; color: #991b1b; border: 0.5px solid #fca5a5; border-radius: 8px; font-size: 13px; cursor: pointer;"
        >
            申請を取り消す
        </button>

        {{-- ===== 拒否済み ===== --}}
        @elseif($isRejected)
        <div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 16px 20px; display: flex; align-items: flex-start; gap: 12px;">
            <span style="font-size: 24px;">❌</span>
            <div>
                <p style="font-size: 14px; font-weight: 500; color: #991b1b; margin: 0;">申請が拒否されました</p>
                <p style="font-size: 12px; color: #991b1b; margin: 6px 0 0; line-height: 1.6;">
                    理由：{{ $dealer->loan_setting_rejected_reason }}
                </p>
            </div>
        </div>

        {{-- 再申請フォーム --}}
        <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; overflow: hidden;">
            <div style="padding: 10px 16px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;">
                <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0;">再申請する</p>
            </div>
            <div style="padding: 14px 16px;">
                <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 6px;">申請理由 <span style="color: #dc2626;">*</span></label>
                <textarea
                    wire:model.live="reason"
                    style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 13px; color: #111827; background: #f9fafb; resize: vertical; min-height: 100px;"
                    placeholder="ローン設定を希望する理由を入力してください（10文字以上）"
                ></textarea>

                {{-- エラー表示を追加 --}}
                @error('reason')
                    <p style="font-size: 12px; color: #dc2626; margin: 4px 0 0;">{{ $message }}</p>
                @enderror

                <button
                    type="button"
                    wire:click="submitRequest"
                    style="margin-top: 10px; width: 100%; padding: 12px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
                >
                    再申請する
                </button>
            </div>
        </div>

        {{-- ===== 未申請 ===== --}}
        @else
        <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; overflow: hidden;">
            <div style="padding: 14px 20px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;">
                <p style="font-size: 14px; font-weight: 500; color: #374151; margin: 0;">ローン設定申請</p>
                <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0;">
                    自社でローンプランを設定するには管理者への申請が必要です。
                    申請内容を確認後、管理者が許可/拒否をお知らせします。
                </p>
            </div>
            <div style="padding: 16px 20px;">
                <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 6px;">申請理由 <span style="color: #dc2626;">*</span></label>
                <textarea
                    wire:model.live="reason"
                    style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 13px; color: #111827; background: #f9fafb; resize: vertical; min-height: 100px;"
                    placeholder="ローン設定を希望する理由を入力してください（10文字以上）"
                ></textarea>

                {{-- エラー表示を追加 --}}
                @error('reason')
                    <p style="font-size: 12px; color: #dc2626; margin: 4px 0 0;">{{ $message }}</p>
                @enderror

                <div style="margin-top: 12px; padding: 10px 14px; background: #fef3c7; border-radius: 8px; border: 0.5px solid #fcd34d;">
                    <p style="font-size: 11px; color: #92400e; margin: 0; line-height: 1.6;">
                        ⚠️ ローン設定を許可された場合、設定したローン情報はディーラー側の責任となります。
                        管理者は車両承認時にローン内容を確認し、問題がある場合は差し戻しを行います。
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="submitRequest"
                    style="margin-top: 12px; width: 100%; padding: 13px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer;"
                >
                    申請する
                </button>
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>