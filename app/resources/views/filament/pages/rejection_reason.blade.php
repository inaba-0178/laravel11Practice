{{--
    このファイルは resources/views/filament/modals/rejection-reason.blade.php に配置
    EditCarRegistration.phpの編集ページで差し戻し時に表示する

    使い方：
    EditCarRegistration.php の getHeaderActions() や contentFooter() から
    $record->status === 'rejected' の場合にこのビューを差し込む
--}}

@if(isset($record) && $record->status === 'rejected' && !empty($record->rejection_reason))
@php
    $reason          = $record->rejection_reason;
    $generalComment  = $reason['general_comment'] ?? '';
    $flaggedImages   = $reason['flagged_images'] ?? [];
    $items           = $reason['items'] ?? [];
@endphp

<div
    x-data="{
        generalResponse: @js($reason['general_response'] ?? ''),
        imageResponses: @js(collect($flaggedImages)->mapWithKeys(fn($img) => [$img['id'] => $img['dealer_response'] ?? ''])->toArray()),
        itemResponses: @js(collect($items)->map(fn($item, $idx) => ['idx' => $idx, 'response' => $item['dealer_response'] ?? ''])->values()->toArray()),
        saving: false,

        async saveResponse() {
            this.saving = true;
            await $wire.saveDealerResponse({
                general_response: this.generalResponse,
                image_responses:  this.imageResponses,
                item_responses:   this.itemResponses.map(r => r.response),
            });
            this.saving = false;
        }
    }"
    style="margin-bottom: 24px;"
>
    {{-- 差し戻し通知ヘッダー --}}
    <div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 14px 18px; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
            <span style="background: #E24B4A; color: white; font-size: 11px; padding: 2px 8px; border-radius: 5px; font-weight: 500;">差し戻し</span>
            <p style="font-size: 13px; font-weight: 500; color: #991b1b; margin: 0;">管理者から差し戻しがあります</p>
        </div>
        <p style="font-size: 12px; color: #7f1d1d; margin: 0;">指摘内容を確認し、対応内容を入力して再度承認依頼してください。</p>
    </div>

    {{-- 全体指摘 --}}
    @if(!empty($generalComment))
    <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; margin-bottom: 12px; overflow: hidden;">
        <div style="padding: 10px 16px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;">
            <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0;">全体的な指摘</p>
        </div>
        <div style="padding: 12px 16px; display: flex; flex-direction: column; gap: 10px;">
            {{-- 管理者の指摘 --}}
            <div style="background: #fef2f2; border-left: 3px solid #E24B4A; border-radius: 0 6px 6px 0; padding: 8px 12px;">
                <p style="font-size: 10px; color: #991b1b; margin: 0 0 3px; font-weight: 500;">管理者からの指摘</p>
                <p style="font-size: 12px; color: #7f1d1d; margin: 0;">{{ $generalComment }}</p>
            </div>
            {{-- ディーラー返答入力 --}}
            <div>
                <label style="font-size: 10px; color: #6b7280; display: block; margin-bottom: 4px;">対応内容を入力</label>
                <textarea
                    x-model="generalResponse"
                    style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 12px; color: #111827; background: #f9fafb; resize: vertical; min-height: 64px;"
                    placeholder="対応した内容を入力してください"
                ></textarea>
            </div>
        </div>
    </div>
    @endif

    {{-- 画像個別指摘 --}}
    @if(!empty($flaggedImages))
    <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; margin-bottom: 12px; overflow: hidden;">
        <div style="padding: 10px 16px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;">
            <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0;">画像の指摘（{{ count($flaggedImages) }}枚）</p>
        </div>
        <div style="padding: 12px 16px; display: flex; flex-direction: column; gap: 12px;">
            @foreach($flaggedImages as $flaggedImg)
            @php
                $imgRecord = \App\Infrastructure\Eloquent\User\StkCarImages::find($flaggedImg['id']);
                $imgUrl    = $imgRecord ? \Illuminate\Support\Facades\Storage::disk('s3')->url($imgRecord->image_url) : null;
                $resolved  = $flaggedImg['resolved'] ?? false;
            @endphp
            <div style="border: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }}; border-radius: 8px; overflow: hidden;">
                <div style="padding: 8px 12px; background: {{ $resolved ? '#f0fdf4' : '#fef2f2' }}; border-bottom: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }}; display: flex; align-items: center; justify-content: space-between;">
                    <p style="font-size: 11px; font-weight: 500; color: {{ $resolved ? '#15803d' : '#991b1b' }}; margin: 0;">
                        画像 ID: {{ $flaggedImg['id'] }}
                        @if($resolved)
                            <span style="font-size: 10px; background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 4px; margin-left: 4px;">対応済み</span>
                        @endif
                    </p>
                </div>
                <div style="padding: 10px 12px; display: flex; gap: 12px; align-items: flex-start;">
                    {{-- 画像サムネイル --}}
                    @if($imgUrl)
                    <div style="width: 80px; height: 60px; border-radius: 6px; overflow: hidden; flex-shrink: 0; border: {{ $resolved ? '0.5px solid #bbf7d0' : '2px solid #E24B4A' }};">
                        <img src="{{ $imgUrl }}" style="width: 100%; height: 100%; object-fit: cover;" />
                    </div>
                    @endif
                    <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                        {{-- 指摘内容 --}}
                        <div style="background: #fef2f2; border-left: 3px solid #E24B4A; border-radius: 0 5px 5px 0; padding: 5px 8px;">
                            <p style="font-size: 10px; color: #991b1b; margin: 0 0 2px; font-weight: 500;">指摘内容</p>
                            <p style="font-size: 11px; color: #7f1d1d; margin: 0;">{{ $flaggedImg['reason'] }}</p>
                        </div>
                        {{-- 返答入力 --}}
                        @if(!$resolved)
                        <div>
                            <label style="font-size: 10px; color: #6b7280; display: block; margin-bottom: 3px;">対応内容を入力</label>
                            <input
                                type="text"
                                x-model="imageResponses[{{ $flaggedImg['id'] }}]"
                                placeholder="例：撮り直してアップロードしました"
                                style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 6px; padding: 5px 8px; font-size: 11px; color: #111827; background: #f9fafb;"
                            />
                        </div>
                        @else
                        <div style="background: #f0f9ff; border-left: 3px solid #185FA5; border-radius: 0 5px 5px 0; padding: 5px 8px;">
                            <p style="font-size: 10px; color: #0369a1; margin: 0 0 2px; font-weight: 500;">対応内容</p>
                            <p style="font-size: 11px; color: #0c4a6e; margin: 0;">{{ $flaggedImg['dealer_response'] ?? '' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 項目指摘 --}}
    @if(!empty($items))
    <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; margin-bottom: 12px; overflow: hidden;">
        <div style="padding: 10px 16px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;">
            <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0;">項目の指摘（{{ count($items) }}件）</p>
        </div>
        <div style="padding: 12px 16px; display: flex; flex-direction: column; gap: 10px;">
            @foreach($items as $idx => $item)
            @php $resolved = $item['resolved'] ?? false; @endphp
            <div style="border: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }}; border-radius: 8px; overflow: hidden;">
                <div style="padding: 8px 12px; background: {{ $resolved ? '#f0fdf4' : '#fef2f2' }}; border-bottom: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }};">
                    <p style="font-size: 11px; font-weight: 500; color: {{ $resolved ? '#15803d' : '#991b1b' }}; margin: 0;">
                        {{ $item['category'] ?? 'その他' }}
                        @if($resolved)
                            <span style="font-size: 10px; background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 4px; margin-left: 4px;">対応済み</span>
                        @endif
                    </p>
                </div>
                <div style="padding: 10px 12px; display: flex; flex-direction: column; gap: 6px;">
                    {{-- 指摘内容 --}}
                    <div style="background: #fef2f2; border-left: 3px solid #E24B4A; border-radius: 0 5px 5px 0; padding: 6px 10px;">
                        <p style="font-size: 10px; color: #991b1b; margin: 0 0 2px; font-weight: 500;">指摘内容</p>
                        <p style="font-size: 11px; color: #7f1d1d; margin: 0;">{{ $item['reason'] ?? '' }}</p>
                    </div>
                    {{-- 返答入力または表示 --}}
                    @if(!$resolved)
                    <div>
                        <label style="font-size: 10px; color: #6b7280; display: block; margin-bottom: 3px;">対応内容を入力</label>
                        <textarea
                            x-model="itemResponses[{{ $idx }}].response"
                            style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 11px; color: #111827; background: #f9fafb; resize: vertical; min-height: 52px;"
                            placeholder="対応した内容を入力してください"
                        ></textarea>
                    </div>
                    @else
                    <div style="background: #f0f9ff; border-left: 3px solid #185FA5; border-radius: 0 5px 5px 0; padding: 6px 10px;">
                        <p style="font-size: 10px; color: #0369a1; margin: 0 0 2px; font-weight: 500;">対応内容</p>
                        <p style="font-size: 11px; color: #0c4a6e; margin: 0;">{{ $item['dealer_response'] ?? '' }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 返答保存ボタン --}}
    <button
        type="button"
        x-on:click="saveResponse"
        x-bind:disabled="saving"
        style="width: 100%; padding: 12px; background: #185FA5; color: #E6F1FB; border: none; border-radius: 10px; font-size: 13px; font-weight: 500; cursor: pointer; margin-bottom: 8px;"
    >
        <span x-show="!saving">対応内容を保存する</span>
        <span x-show="saving">保存中...</span>
    </button>
</div>
@endif