<x-filament-panels::page>
    @php
        $carData    = $this->getCarData();
        $car        = $carData['car'];
        $series     = $carData['series'];
        $vehicle    = $carData['vehicle'];
        $images     = $this->getImages();
        $equipCats  = $this->getEquipmentByCategory();
        $detail     = $car->detail;
        $dealer     = $car->dealer;
        $rejCats    = $this->getRejectionCategories();
    @endphp

    <div style="display: grid; grid-template-columns: 1fr 300px 280px; gap: 14px; align-items: start;">

        {{-- ==================== 左：車両詳細 ==================== --}}
        <div style="display: flex; flex-direction: column; gap: 14px;">

            {{-- ヘッダー --}}
            <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="font-size: 15px; font-weight: 500; margin: 0; color: #111827;">
                        {{ $series?->series_name }} {{ $vehicle?->name }}
                        {{ $car->model_year ? $car->model_year.'年式' : '' }}
                    </p>
                    <p style="font-size: 11px; color: #6b7280; margin: 3px 0 0;">
                        {{ $dealer?->name }} ／ #{{ $car->id }} ／ 申請日：{{ $car->created_at?->format('Y/m/d H:i') }}
                    </p>
                </div>
                <span style="background: #fef3c7; color: #92400e; font-size: 11px; padding: 3px 10px; border-radius: 8px; font-weight: 500;">
                    {{ \App\Constants\CarStatus::LABELS[$car->status] ?? $car->status }}
                </span>
            </div>

            {{-- 画像スライダー --}}
            @if(count($images) > 0)
            <div
                style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 14px 18px;"
                x-data="{
                    current: 0,
                    thumbPage: 0,
                    perPage: 10,
                    images: @js($images),
                    flagged: @js($flaggedImages),
                    get totalPages() { return Math.ceil(this.images.length / this.perPage) },
                    get thumbImages() { return this.images.slice(this.thumbPage * this.perPage, (this.thumbPage + 1) * this.perPage) },
                    isFlagged(id) { return this.flagged.some(f => f.id === id) },
                    isCurrentThumb(idx) { return (this.thumbPage * this.perPage + idx) === this.current },
                    prev() { this.current = this.current === 0 ? this.images.length - 1 : this.current - 1; this.syncThumbPage() },
                    next() { this.current = this.current === this.images.length - 1 ? 0 : this.current + 1; this.syncThumbPage() },
                    select(idx) { this.current = this.thumbPage * this.perPage + idx },
                    syncThumbPage() { this.thumbPage = Math.floor(this.current / this.perPage) },
                }"
            >
                <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0 0 10px;">登録画像（{{ count($images) }}枚）</p>

                {{-- メイン画像 --}}
                <div style="position: relative; background: #f3f4f6; border-radius: 10px; overflow: hidden; aspect-ratio: 16/9; margin-bottom: 10px;">
                    <template x-for="(img, idx) in images" :key="idx">
                        <div x-show="current === idx" style="width: 100%; height: 100%;">
                            <img :src="img.url" style="width: 100%; height: 100%; object-fit: contain; background: #f3f4f6;" />
                            <template x-if="isFlagged(img.id)">
                                <div style="position: absolute; top: 10px; right: 10px; background: #E24B4A; color: white; font-size: 11px; padding: 3px 10px; border-radius: 6px; font-weight: 500;">指摘あり</div>
                            </template>
                        </div>
                    </template>
                    <button type="button" x-on:click="prev" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); border: none; border-radius: 50%; width: 34px; height: 34px; color: white; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center;">‹</button>
                    <button type="button" x-on:click="next" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); border: none; border-radius: 50%; width: 34px; height: 34px; color: white; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center;">›</button>
                    <div style="position: absolute; bottom: 8px; right: 12px; background: rgba(0,0,0,0.55); color: white; font-size: 11px; padding: 2px 8px; border-radius: 6px;">
                        <span x-text="current + 1"></span> / {{ count($images) }}
                    </div>
                </div>

                {{-- サムネイル --}}
                <div style="display: flex; align-items: center; gap: 5px;">
                    <button type="button" x-on:click="thumbPage = Math.max(0, thumbPage - 1)" x-bind:disabled="thumbPage === 0" x-bind:style="thumbPage === 0 ? 'opacity:0.3;cursor:not-allowed;' : 'cursor:pointer;'" style="background: #f3f4f6; border: 0.5px solid #e5e7eb; border-radius: 6px; width: 26px; height: 26px; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">‹</button>
                    <div style="flex: 1; display: grid; grid-template-columns: repeat(10, 1fr); gap: 3px;">
                        <template x-for="(img, idx) in thumbImages" :key="img.id">
                            <div x-on:click="select(idx)" style="cursor: pointer; position: relative;">
                                <div :style="isCurrentThumb(idx) ? 'border-radius:5px;border:2px solid #185FA5;height:44px;overflow:hidden;' : (isFlagged(img.id) ? 'border-radius:5px;border:2px solid #E24B4A;height:44px;overflow:hidden;background:#fef2f2;' : 'border-radius:5px;border:0.5px solid #e5e7eb;height:44px;overflow:hidden;background:#f3f4f6;')">
                                    <img :src="img.url" style="width:100%;height:100%;object-fit:cover;" />
                                </div>
                                <template x-if="isFlagged(img.id)">
                                    <span style="position:absolute;top:1px;right:1px;background:#E24B4A;color:white;font-size:7px;width:11px;height:11px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:500;">!</span>
                                </template>
                            </div>
                        </template>
                    </div>
                    <button type="button" x-on:click="thumbPage = Math.min(totalPages - 1, thumbPage + 1)" x-bind:disabled="thumbPage >= totalPages - 1" x-bind:style="thumbPage >= totalPages - 1 ? 'opacity:0.3;cursor:not-allowed;' : 'cursor:pointer;'" style="background: #f3f4f6; border: 0.5px solid #e5e7eb; border-radius: 6px; width: 26px; height: 26px; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">›</button>
                </div>
            </div>
            @endif

            {{-- 車両の状態 --}}
            <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 14px 18px;">
                <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0 0 10px; padding-left: 10px; border-left: 3px solid #185FA5;">車両の状態</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e5e7eb; border: 0.5px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                    @foreach([
                        ['年式', $car->model_year ? $car->model_year.'年' : '-'],
                        ['走行距離', number_format($car->mileage).'km'],
                        ['修復歴', \App\Constants\RepairHistory::LABELS[$car->repair_history] ?? '-'],
                        ['支払価格', number_format($car->price).'円'],
                        ['リサイクル預託金', $car->recycle_fee ? number_format((int)$car->recycle_fee).'円' : '-'],
                        ['色', $car->color ?? '-'],
                        ['車検満了日', $detail?->inspection_expire_date ?? '-'],
                        ['駆動方式', $detail?->drive_system ?? '-'],
                        ['排気量', $detail?->displacement ? number_format($detail->displacement).'cc' : '-'],
                        ['ミッション', $car->transmission ?? '-'],
                        ['ハンドル', \App\Constants\SteeringWheel::LABELS[$detail?->steering_wheel ?? ''] ?? '-'],
                        ['ドア数', $detail?->number_of_doors ? $detail->number_of_doors.'ドア' : '-'],
                        ['乗車定員', $detail?->riding_capacity ? $detail->riding_capacity.'名' : '-'],
                    ] as [$label, $value])
                    <div style="display: grid; grid-template-columns: 1fr 2fr; background: white;">
                        <div style="padding: 8px 12px; font-size: 11px; color: #6b7280; background: #f9fafb;">{{ $label }}</div>
                        <div style="padding: 8px 12px; font-size: 12px; color: #111827;">{{ $value }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- 装備仕様（カテゴリ分け） --}}
            <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 14px 18px;">
                <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0 0 12px; padding-left: 10px; border-left: 3px solid #185FA5;">装備仕様</p>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($equipCats as $catKey => $catData)
                        @if(count($catData['items']) > 0)
                        <div style="border: 0.5px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
                            <div style="padding: 8px 14px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;">
                                <p style="font-size: 11px; font-weight: 500; color: #374151; margin: 0;">{{ $catData['label'] }}</p>
                            </div>
                            <div style="padding: 10px 14px; display: flex; flex-wrap: wrap; gap: 5px;">
                                @foreach($catData['items'] as $item)
                                    <span style="padding: 4px 10px; border-radius: 6px; font-size: 11px;
                                        {{ $item['is_equipped']
                                            ? 'background: #fef2f2; color: #991b1b; border: 0.5px solid #fca5a5;'
                                            : 'background: #f3f4f6; color: #9ca3af; border: 0.5px solid #e5e7eb;'
                                        }}">
                                        {{ $item['label'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- ローン設定 --}}
            @php $loans = $this->getLoans(); @endphp
            <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 14px 18px;">
                <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0 0 12px; padding-left: 10px; border-left: 3px solid #185FA5;">ローン設定</p>
            
                @if(count($loans) === 0)
                    <p style="font-size: 12px; color: #9ca3af;">ローン情報がありません</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        @foreach($loans as $loan)
                        <div style="border: 0.5px solid {{ $loan['is_system_default'] ? '#d1d5db' : '#bae6fd' }}; border-radius: 10px; overflow: hidden;">
                            <div style="padding: 8px 14px; background: {{ $loan['is_system_default'] ? '#f9fafb' : '#f0f9ff' }}; border-bottom: 0.5px solid {{ $loan['is_system_default'] ? '#d1d5db' : '#bae6fd' }}; display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <p style="font-size: 11px; font-weight: 500; color: {{ $loan['is_system_default'] ? '#6b7280' : '#0369a1' }}; margin: 0;">
                                        {{ $loan['plan_name'] }}
                                    </p>
                                    @if($loan['is_system_default'])
                                        <span style="font-size: 10px; background: #f3f4f6; color: #6b7280; padding: 1px 6px; border-radius: 4px; border: 0.5px solid #d1d5db;">システムデフォルト</span>
                                    @endif
                                    @if($loan['is_contracted'])
                                        <span style="font-size: 10px; background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 4px;">契約済み</span>
                                    @endif
                                </div>
                                @if($loan['monthly_payment'])
                                <p style="font-size: 12px; font-weight: 500; color: #185FA5; margin: 0;">
                                    月々約 {{ number_format($loan['monthly_payment']) }}円〜
                                </p>
                                @endif
                            </div>
                            <div style="padding: 10px 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 6px;">
                                <div style="font-size: 11px; color: #6b7280;">
                                    金利：{{ $loan['rate'] }}%
                                    @if($loan['is_default_rate'])
                                        <span style="font-size: 10px; color: #9ca3af;">（デフォルト）</span>
                                    @endif
                                </div>
                                <div style="font-size: 11px; color: #6b7280;">
                                    回数：{{ $loan['min_months'] }}〜{{ $loan['max_months'] }}回
                                </div>
                                @if($loan['down_payment'])
                                <div style="font-size: 11px; color: #6b7280;">頭金：{{ number_format($loan['down_payment']) }}円</div>
                                @endif
                                @if($loan['misc_fee'])
                                <div style="font-size: 11px; color: #6b7280;">諸費用：{{ number_format($loan['misc_fee']) }}円</div>
                                @endif
                                @if($loan['bonus_amount'] && $loan['bonus_times'])
                                <div style="font-size: 11px; color: #6b7280; grid-column: span 2;">
                                    ボーナス：{{ number_format($loan['bonus_amount']) }}円 × 年{{ $loan['bonus_times'] }}回
                                </div>
                                @endif
                                @if($loan['note'])
                                <div style="font-size: 11px; color: #6b7280; grid-column: span 2;">備考：{{ $loan['note'] }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- その他オプション --}}
            @php $otherOptions = $this->getOtherOptions(); @endphp
            @if(count($otherOptions) > 0)
            <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 14px 18px;">
                <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0 0 12px; padding-left: 10px; border-left: 3px solid #185FA5;">その他オプション</p>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($otherOptions as $catKey => $catData)
                    <div style="border: 0.5px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
                        <div style="padding: 8px 14px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;">
                            <p style="font-size: 11px; font-weight: 500; color: #374151; margin: 0;">{{ $catData['label'] }}</p>
                        </div>
                        <div style="padding: 10px 14px;">
                            @foreach($catData['items'] as $item)
                            <p style="font-size: 12px; color: #374151; margin: 0 0 4px;">・{{ $item }}</p>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- 説明文 --}}
            @if($detail?->description)
            <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 14px 18px;">
                <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0 0 8px; padding-left: 10px; border-left: 3px solid #185FA5;">車両説明文</p>
                <p style="font-size: 12px; color: #374151; line-height: 1.7; margin: 0;">{{ $detail->description }}</p>
            </div>
            @endif
        </div>

        {{-- ==================== 中：差し戻し入力 ==================== --}}
        <div style="position: sticky; top: 20px; display: flex; flex-direction: column; gap: 10px;">

            {{-- 承認ボタン --}}
            <button type="button" wire:click="approve" wire:confirm="この車両を承認しますか？" style="width: 100%; padding: 13px; background: #3B6D11; color: #EAF3DE; border: none; border-radius: 10px; font-size: 14px; font-weight: 500; cursor: pointer;">
                承認する
            </button>

            {{-- 差し戻しパネル --}}
            <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; overflow: hidden;">
                <div style="padding: 10px 14px; background: #fef2f2; border-bottom: 0.5px solid #fca5a5;">
                    <p style="font-size: 12px; font-weight: 500; margin: 0; color: #991b1b;">差し戻し内容を入力</p>
                </div>
                <div style="padding: 14px; display: flex; flex-direction: column; gap: 14px;">

                    {{-- 全体指摘 --}}
                    <div>
                        <label style="font-size: 11px; color: #6b7280; display: block; margin-bottom: 4px;">全体的な指摘内容</label>
                        <textarea wire:model.lazy="generalComment" style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 12px; color: #111827; background: #f9fafb; resize: vertical; min-height: 70px;" placeholder="例：全体的に写真が暗い状態です"></textarea>
                    </div>

                    {{-- 画像個別指摘 --}}
                    @if(count($images) > 0)
                    <div>
                        <label style="font-size: 11px; color: #6b7280; display: block; margin-bottom: 4px;">画像ごとの指摘</label>
                        <p style="font-size: 10px; color: #9ca3af; margin: 0 0 8px;">画像をクリックして選択・解除</p>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px; margin-bottom: 10px;">
                            @foreach($images as $image)
                                @php $flagged = $this->isFlagged($image['id']); @endphp
                                <div wire:click="toggleImageFlag({{ $image['id'] }})" style="aspect-ratio: 4/3; border-radius: 6px; border: {{ $flagged ? '2px solid #E24B4A' : '0.5px solid #e5e7eb' }}; overflow: hidden; cursor: pointer; position: relative; background: {{ $flagged ? '#fef2f2' : '#f3f4f6' }};">
                                    <img src="{{ $image['url'] }}" style="width: 100%; height: 100%; object-fit: cover; opacity: {{ $flagged ? '0.8' : '1' }};" />
                                    @if($flagged)
                                        <span style="position: absolute; top: 2px; right: 2px; background: #E24B4A; color: white; font-size: 7px; width: 13px; height: 13px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 500;">!</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @foreach($flaggedImages as $flaggedImg)
                            @if(!($flaggedImg['resolved'] ?? false))
                            <div style="border: 0.5px solid #fca5a5; border-radius: 8px; padding: 8px 10px; background: #fef2f2; margin-bottom: 6px;">
                                <p style="font-size: 10px; color: #991b1b; margin: 0 0 5px; font-weight: 500;">画像 ID: {{ $flaggedImg['id'] }}</p>
                                <input type="text" value="{{ $flaggedImg['reason'] }}" wire:change="updateImageReason({{ $flaggedImg['id'] }}, $event.target.value)" placeholder="指摘理由を入力" style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 6px; padding: 5px 8px; font-size: 11px; background: white; color: #111827;" />
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endif

                    {{-- 項目指摘 --}}
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label style="font-size: 11px; color: #6b7280;">項目ごとの指摘</label>
                            <button type="button" wire:click="addRejectionItem" style="font-size: 11px; color: #185FA5; background: none; border: 0.5px solid #185FA5; border-radius: 6px; padding: 3px 8px; cursor: pointer;">＋ 追加</button>
                        </div>
                        @foreach($rejectionItems as $idx => $item)
                            @if(!($item['resolved'] ?? false))
                            <div style="border: 0.5px solid #e5e7eb; border-radius: 8px; padding: 10px; margin-bottom: 8px; background: #f9fafb;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                    <select wire:change="updateRejectionItem({{ $idx }}, 'category', $event.target.value)" style="font-size: 11px; border: 0.5px solid #d1d5db; border-radius: 6px; padding: 4px 8px; background: white; color: #111827; width: 140px;">
                                        <option value="">カテゴリ選択</option>
                                        @foreach($rejCats as $cat)
                                            <option value="{{ $cat }}" {{ ($item['category'] ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" wire:click="removeRejectionItem({{ $idx }})" style="font-size: 11px; color: #dc2626; background: none; border: none; cursor: pointer; padding: 0 4px;">✕</button>
                                </div>
                                <textarea wire:change="updateRejectionItem({{ $idx }}, 'reason', $event.target.value)" style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 11px; color: #111827; background: white; resize: vertical; min-height: 54px;" placeholder="指摘内容を入力">{{ $item['reason'] ?? '' }}</textarea>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- 差し戻しボタン --}}
                    <button type="button" wire:click="reject" wire:confirm="差し戻しますか？ディーラーに通知されます。" style="width: 100%; padding: 11px; background: #E24B4A; color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 500; cursor: pointer;">
                        差し戻す
                    </button>
                </div>
            </div>
        </div>

        {{-- ==================== 右：ディーラー返答確認 ==================== --}}
        <div style="position: sticky; top: 20px; display: flex; flex-direction: column; gap: 10px;">
            <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; overflow: hidden;">
                <div style="padding: 10px 14px; background: #f0f9ff; border-bottom: 0.5px solid #bae6fd;">
                    <p style="font-size: 12px; font-weight: 500; margin: 0; color: #0c4a6e;">ディーラー返答確認</p>
                    <p style="font-size: 10px; color: #0369a1; margin: 3px 0 0;">各項目の対応を確認して完了マークを付けてください</p>
                </div>
                <div style="padding: 14px; display: flex; flex-direction: column; gap: 12px;">

                    {{-- 全体指摘への返答 --}}
                    @if(!empty($generalComment))
                    <div style="border: 0.5px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                        <div style="padding: 8px 12px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;">
                            <p style="font-size: 11px; font-weight: 500; color: #374151; margin: 0;">全体指摘</p>
                        </div>
                        <div style="padding: 10px 12px;">
                            <p style="font-size: 11px; color: #6b7280; margin: 0 0 6px; background: #fef2f2; padding: 6px 8px; border-radius: 6px; border-left: 3px solid #E24B4A;">{{ $generalComment }}</p>
                            @php $generalResponse = $record->rejection_reason['general_response'] ?? ''; @endphp
                            @if(!empty($generalResponse))
                                <p style="font-size: 11px; color: #374151; margin: 0; background: #f0f9ff; padding: 6px 8px; border-radius: 6px; border-left: 3px solid #185FA5;">{{ $generalResponse }}</p>
                            @else
                                <p style="font-size: 11px; color: #9ca3af; margin: 0; font-style: italic;">返答待ち</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- 画像指摘への返答 --}}
                    @foreach($flaggedImages as $flaggedImg)
                    <div style="border: 0.5px solid {{ ($flaggedImg['resolved'] ?? false) ? '#bbf7d0' : '#e5e7eb' }}; border-radius: 8px; overflow: hidden;">
                        <div style="padding: 8px 12px; background: {{ ($flaggedImg['resolved'] ?? false) ? '#f0fdf4' : '#f9fafb' }}; border-bottom: 0.5px solid {{ ($flaggedImg['resolved'] ?? false) ? '#bbf7d0' : '#e5e7eb' }}; display: flex; justify-content: space-between; align-items: center;">
                            <p style="font-size: 11px; font-weight: 500; color: {{ ($flaggedImg['resolved'] ?? false) ? '#15803d' : '#374151' }}; margin: 0;">
                                画像 ID: {{ $flaggedImg['id'] }}
                                @if($flaggedImg['resolved'] ?? false)
                                    <span style="font-size: 10px; background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 4px; margin-left: 4px;">完了</span>
                                @endif
                            </p>
                            @if(!($flaggedImg['resolved'] ?? false))
                                <button type="button" wire:click="resolveImage({{ $flaggedImg['id'] }})" style="font-size: 10px; color: #15803d; background: #dcfce7; border: 0.5px solid #bbf7d0; border-radius: 5px; padding: 2px 8px; cursor: pointer;">完了にする</button>
                            @endif
                        </div>
                        <div style="padding: 10px 12px; display: flex; flex-direction: column; gap: 5px;">
                            <p style="font-size: 11px; color: #6b7280; margin: 0; background: #fef2f2; padding: 5px 8px; border-radius: 5px; border-left: 3px solid #E24B4A;">{{ $flaggedImg['reason'] }}</p>
                            @if(!empty($flaggedImg['dealer_response'] ?? ''))
                                <p style="font-size: 11px; color: #374151; margin: 0; background: #f0f9ff; padding: 5px 8px; border-radius: 5px; border-left: 3px solid #185FA5;">{{ $flaggedImg['dealer_response'] }}</p>
                            @else
                                <p style="font-size: 11px; color: #9ca3af; margin: 0; font-style: italic;">返答待ち</p>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    {{-- 項目指摘への返答 --}}
                    @foreach($rejectionItems as $idx => $item)
                    <div style="border: 0.5px solid {{ ($item['resolved'] ?? false) ? '#bbf7d0' : '#e5e7eb' }}; border-radius: 8px; overflow: hidden;">
                        <div style="padding: 8px 12px; background: {{ ($item['resolved'] ?? false) ? '#f0fdf4' : '#f9fafb' }}; border-bottom: 0.5px solid {{ ($item['resolved'] ?? false) ? '#bbf7d0' : '#e5e7eb' }}; display: flex; justify-content: space-between; align-items: center;">
                            <p style="font-size: 11px; font-weight: 500; color: {{ ($item['resolved'] ?? false) ? '#15803d' : '#374151' }}; margin: 0;">
                                {{ $item['category'] ?? 'その他' }}
                                @if($item['resolved'] ?? false)
                                    <span style="font-size: 10px; background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 4px; margin-left: 4px;">完了</span>
                                @endif
                            </p>
                            @if(!($item['resolved'] ?? false))
                                <button type="button" wire:click="resolveItem({{ $idx }})" style="font-size: 10px; color: #15803d; background: #dcfce7; border: 0.5px solid #bbf7d0; border-radius: 5px; padding: 2px 8px; cursor: pointer;">完了にする</button>
                            @endif
                        </div>
                        <div style="padding: 10px 12px; display: flex; flex-direction: column; gap: 5px;">
                            <p style="font-size: 11px; color: #6b7280; margin: 0; background: #fef2f2; padding: 5px 8px; border-radius: 5px; border-left: 3px solid #E24B4A;">{{ $item['reason'] ?? '' }}</p>
                            @if(!empty($item['dealer_response'] ?? ''))
                                <p style="font-size: 11px; color: #374151; margin: 0; background: #f0f9ff; padding: 5px 8px; border-radius: 5px; border-left: 3px solid #185FA5;">{{ $item['dealer_response'] }}</p>
                            @else
                                <p style="font-size: 11px; color: #9ca3af; margin: 0; font-style: italic;">返答待ち</p>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    @if(empty($generalComment) && empty($flaggedImages) && empty($rejectionItems))
                        <p style="font-size: 12px; color: #9ca3af; text-align: center; padding: 20px 0;">差し戻し内容がありません</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>