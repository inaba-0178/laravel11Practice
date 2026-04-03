<x-filament-panels::page>
    @php
        $record       = $this->getRecord();
        $statusConfig = \App\Constants\CarStatus::STATUS_CONFIG[$record->status] ?? [
            'label'       => $record->status,
            'bg'          => '#f3f4f6',
            'color'       => '#374151',
            'border'      => '#d1d5db',
            'icon'        => '',
            'description' => '',
        ];
    @endphp

    {{-- ===== ステータスバー ===== --}}
    <div style="background: {{ $statusConfig['bg'] }}; border: 1px solid {{ $statusConfig['border'] }}; border-radius: 10px; padding: 14px 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <span style="font-size: 22px; line-height: 1;">{{ $statusConfig['icon'] }}</span>
        <div>
            <p style="font-size: 14px; font-weight: 500; color: {{ $statusConfig['color'] }}; margin: 0;">
                現在のステータス：{{ $statusConfig['label'] }}
            </p>
            @if(!empty($statusConfig['description']))
            <p style="font-size: 12px; color: {{ $statusConfig['color'] }}; margin: 3px 0 0; opacity: 0.8;">
                {{ $statusConfig['description'] }}
            </p>
            @endif
        </div>
    </div>

    {{-- ===== 差し戻し内容表示（rejectedの場合のみ） ===== --}}
    @if($record->status === 'rejected' && !empty($record->rejection_reason))
    @php
        $reason         = $record->rejection_reason;
        $generalComment = $reason['general_comment'] ?? '';
        $flaggedImages  = $reason['flagged_images'] ?? [];
        $items          = $reason['items'] ?? [];
    @endphp

    <div
        x-data="{
            generalResponse: @js($reason['general_response'] ?? ''),
            imageResponses: @js(collect($flaggedImages)->mapWithKeys(fn($img) => [$img['id'] => $img['dealer_response'] ?? ''])->toArray()),
            itemResponses: @js(collect($items)->map(fn($item) => $item['dealer_response'] ?? '')->values()->toArray()),
            saving: false,
            async saveResponse() {
                this.saving = true;
                await $wire.saveDealerResponse({
                    general_response: this.generalResponse,
                    image_responses:  this.imageResponses,
                    item_responses:   this.itemResponses,
                });
                this.saving = false;
            }
        }"
        style="margin-bottom: 24px;"
    >
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">

            {{-- 左：指摘内容 --}}
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0; padding-left: 10px; border-left: 3px solid #E24B4A;">指摘内容</p>

                {{-- 全体指摘 --}}
                @if(!empty($generalComment))
                <div style="background: white; border-radius: 10px; border: 0.5px solid #fca5a5; overflow: hidden;">
                    <div style="padding: 8px 14px; background: #fef2f2; border-bottom: 0.5px solid #fca5a5;">
                        <p style="font-size: 11px; font-weight: 500; color: #991b1b; margin: 0;">全体的な指摘</p>
                    </div>
                    <div style="padding: 12px 14px;">
                        <p style="font-size: 12px; color: #7f1d1d; margin: 0; line-height: 1.6;">{{ $generalComment }}</p>
                    </div>
                </div>
                @endif

                {{-- 画像指摘 --}}
                @foreach($flaggedImages as $flaggedImg)
                @php
                    $imgRecord = \App\Infrastructure\Eloquent\User\StkCarImages::find($flaggedImg['id']);
                    $imgUrl    = $imgRecord ? \Illuminate\Support\Facades\Storage::disk('s3')->url($imgRecord->image_url) : null;
                    $resolved  = $flaggedImg['resolved'] ?? false;
                @endphp
                <div style="background: white; border-radius: 10px; border: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }}; overflow: hidden;">
                    <div style="padding: 8px 14px; background: {{ $resolved ? '#f0fdf4' : '#fef2f2' }}; border-bottom: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }}; display: flex; align-items: center; gap: 8px;">
                        <p style="font-size: 11px; font-weight: 500; color: {{ $resolved ? '#15803d' : '#991b1b' }}; margin: 0;">画像の指摘</p>
                        @if($resolved)
                            <span style="font-size: 10px; background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 4px;">対応済み</span>
                        @endif
                    </div>
                    <div style="padding: 12px 14px; display: flex; gap: 12px; align-items: flex-start;">
                        @if($imgUrl)
                        <div style="width: 80px; height: 60px; border-radius: 6px; overflow: hidden; flex-shrink: 0; border: {{ $resolved ? '0.5px solid #bbf7d0' : '2px solid #E24B4A' }};">
                            <img src="{{ $imgUrl }}" style="width: 100%; height: 100%; object-fit: cover;" />
                        </div>
                        @endif
                        <p style="font-size: 12px; color: #7f1d1d; margin: 0; line-height: 1.6;">{{ $flaggedImg['reason'] }}</p>
                    </div>
                </div>
                @endforeach

                {{-- 項目指摘 --}}
                @foreach($items as $idx => $item)
                @php $resolved = $item['resolved'] ?? false; @endphp
                <div style="background: white; border-radius: 10px; border: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }}; overflow: hidden;">
                    <div style="padding: 8px 14px; background: {{ $resolved ? '#f0fdf4' : '#fef2f2' }}; border-bottom: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }}; display: flex; align-items: center; gap: 8px;">
                        <p style="font-size: 11px; font-weight: 500; color: {{ $resolved ? '#15803d' : '#991b1b' }}; margin: 0;">{{ $item['category'] ?? 'その他' }}</p>
                        @if($resolved)
                            <span style="font-size: 10px; background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 4px;">対応済み</span>
                        @endif
                    </div>
                    <div style="padding: 12px 14px;">
                        <p style="font-size: 12px; color: #7f1d1d; margin: 0; line-height: 1.6;">{{ $item['reason'] ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- 右：対応内容入力 --}}
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0; padding-left: 10px; border-left: 3px solid #185FA5;">対応内容を入力</p>

                {{-- 全体指摘への返答 --}}
                @if(!empty($generalComment))
                <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 8px 14px; background: #f0f9ff; border-bottom: 0.5px solid #bae6fd;">
                        <p style="font-size: 11px; font-weight: 500; color: #0369a1; margin: 0;">全体指摘への対応</p>
                    </div>
                    <div style="padding: 12px 14px;">
                        <textarea
                            x-model="generalResponse"
                            style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 12px; color: #111827; background: #f9fafb; resize: vertical; min-height: 72px;"
                            placeholder="対応した内容を入力してください"
                        ></textarea>
                    </div>
                </div>
                @endif

                {{-- 画像指摘への返答 --}}
                @foreach($flaggedImages as $flaggedImg)
                @php $resolved = $flaggedImg['resolved'] ?? false; @endphp
                <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 8px 14px; background: #f0f9ff; border-bottom: 0.5px solid #bae6fd;">
                        <p style="font-size: 11px; font-weight: 500; color: #0369a1; margin: 0;">画像指摘への対応</p>
                    </div>
                    <div style="padding: 12px 14px;">
                        @if($resolved)
                            <p style="font-size: 12px; color: #15803d; margin: 0; background: #f0fdf4; padding: 8px 10px; border-radius: 6px; border-left: 3px solid #22c55e;">{{ $flaggedImg['dealer_response'] ?? '対応済み' }}</p>
                        @else
                            <input
                                type="text"
                                x-model="imageResponses[{{ $flaggedImg['id'] }}]"
                                placeholder="例：撮り直してアップロードしました"
                                style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 12px; color: #111827; background: #f9fafb;"
                            />
                        @endif
                    </div>
                </div>
                @endforeach

                {{-- 項目指摘への返答 --}}
                @foreach($items as $idx => $item)
                @php $resolved = $item['resolved'] ?? false; @endphp
                <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; overflow: hidden;">
                    <div style="padding: 8px 14px; background: #f0f9ff; border-bottom: 0.5px solid #bae6fd;">
                        <p style="font-size: 11px; font-weight: 500; color: #0369a1; margin: 0;">{{ $item['category'] ?? 'その他' }}への対応</p>
                    </div>
                    <div style="padding: 12px 14px;">
                        @if($resolved)
                            <p style="font-size: 12px; color: #15803d; margin: 0; background: #f0fdf4; padding: 8px 10px; border-radius: 6px; border-left: 3px solid #22c55e;">{{ $item['dealer_response'] ?? '対応済み' }}</p>
                        @else
                            <textarea
                                x-model="itemResponses[{{ $idx }}]"
                                style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 12px; color: #111827; background: #f9fafb; resize: vertical; min-height: 60px;"
                                placeholder="対応した内容を入力してください"
                            ></textarea>
                        @endif
                    </div>
                </div>
                @endforeach

                {{-- 対応内容保存ボタン --}}
                <button
                    type="button"
                    x-on:click="saveResponse"
                    x-bind:disabled="saving"
                    style="width: 100%; padding: 12px; background: #185FA5; color: #E6F1FB; border: none; border-radius: 10px; font-size: 13px; font-weight: 500; cursor: pointer;"
                >
                    <span x-show="!saving">対応内容を保存する</span>
                    <span x-show="saving">保存中...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== 通常のFilamentフォーム ===== --}}
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>