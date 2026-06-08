<x-filament-panels::page>
    @php
        $images       = $this->getImages();
        $equipSections = $this->getEquipmentSections();
        $vehicleSpec  = $this->getVehicleSpec();
        $loan         = $this->getLoan();
        $car          = $this->record;
        $detail       = $car->detail;
        $dealer       = $car->dealer;

        $statusConfig = \App\Constants\CarStatus::STATUS_CONFIG[$car->status] ?? [
            'label'       => \App\Constants\CarStatus::LABELS[$car->status] ?? $car->status,
            'bg'          => '#f3f4f6',
            'color'       => '#374151',
            'border'      => '#d1d5db',
            'icon'        => '',
            'description' => '',
        ];
    @endphp

    {{-- ===== ステータスバー ===== --}}
    <div style="background: {{ $statusConfig['bg'] }}; border: 1px solid {{ $statusConfig['border'] }}; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 18px;">{{ $statusConfig['icon'] }}</span>
        <div>
            <p style="font-size: 13px; font-weight: 500; color: {{ $statusConfig['color'] }}; margin: 0;">
                現在のステータス：{{ $statusConfig['label'] }}
                @if(in_array($car->status, [
                    \App\Constants\CarStatus::SCHEDULED,
                    \App\Constants\CarStatus::AVAILABLE,
                    \App\Constants\CarStatus::PUBLISH_ENDED,
                ]) && $car->published_at)
                    <span style="font-size: 11px; margin-left: 8px; {{ $car->status === \App\Constants\CarStatus::PUBLISH_ENDED ? 'color: #9ca3af;' : '' }}">
                        {{ $car->published_at->format('Y/m/d H:i') }}
                        〜
                        {{ $car->publish_end_at ? $car->publish_end_at->format('Y/m/d H:i') : '期間未設定' }}
                    </span>
                @endif
            </p>
        </div>
    </div>

    {{-- ===== メインエリア ===== --}}
    <div
        style="background: #0a0a0a; border-radius: 12px; padding: 32px; font-family: 'Montserrat', sans-serif;"
        x-data="{
            images: @js($images),
            currentIndex: 0,
            thumbPage: 0,
            thumbPerPage: 10,
            imageCategory: 'all',
            openSections: @js(array_column($equipSections, 'key')),

            get filteredImages() {
                if (this.imageCategory === 'all') return this.images;
                return this.images.filter(img => img.type === this.imageCategory);
            },
            get currentImage() {
                return this.filteredImages[this.currentIndex] ?? null;
            },
            get totalThumbPages() {
                return Math.ceil(this.filteredImages.length / this.thumbPerPage);
            },
            get thumbImages() {
                const start = this.thumbPage * this.thumbPerPage;
                return this.filteredImages.slice(start, start + this.thumbPerPage);
            },
            prev() {
                this.currentIndex = this.currentIndex === 0
                    ? this.filteredImages.length - 1
                    : this.currentIndex - 1;
                this.syncThumbPage();
            },
            next() {
                this.currentIndex = this.currentIndex === this.filteredImages.length - 1
                    ? 0
                    : this.currentIndex + 1;
                this.syncThumbPage();
            },
            selectThumb(idx) {
                this.currentIndex = this.thumbPage * this.thumbPerPage + idx;
            },
            syncThumbPage() {
                this.thumbPage = Math.floor(this.currentIndex / this.thumbPerPage);
            },
            isCurrentThumb(idx) {
                return (this.thumbPage * this.thumbPerPage + idx) === this.currentIndex;
            },
            toggleSection(key) {
                const idx = this.openSections.indexOf(key);
                idx === -1 ? this.openSections.push(key) : this.openSections.splice(idx, 1);
            },
        }"
    >
        {{-- 車両名 --}}
        <div style="margin-bottom: 24px;">
            <h1 style="font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 300; color: #fff; letter-spacing: 0.1em; margin: 0 0 6px;">
                {{ $car->manufacturer?->display_name }} {{ $car->series?->series_name }}
            </h1>
            <p style="font-size: 13px; color: #888; margin: 0;">{{ $car->vehicle?->name }}</p>
        </div>

        {{-- メインエリア 7:3 --}}
        <div style="display: grid; grid-template-columns: 7fr 3fr; gap: 24px; margin-bottom: 48px;">

            {{-- 左：画像エリア --}}
            <div style="display: flex; flex-direction: column; gap: 12px;">

                {{-- メイン画像 --}}
                <div style="position: relative; background: #111; border-radius: 8px; overflow: hidden; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center;">
                    <template x-if="currentImage">
                        <img :src="currentImage.url" style="width: 100%; height: 100%; object-fit: cover;" />
                    </template>
                    <template x-if="!currentImage">
                        <span style="font-size: 12px; color: #444;">NO IMAGE</span>
                    </template>
                    <template x-if="filteredImages.length > 1">
                        <button x-on:click="prev" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); border: none; border-radius: 50%; width: 36px; height: 36px; color: white; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center;">‹</button>
                    </template>
                    <template x-if="filteredImages.length > 1">
                        <button x-on:click="next" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); border: none; border-radius: 50%; width: 36px; height: 36px; color: white; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center;">›</button>
                    </template>
                    <div style="position: absolute; bottom: 8px; right: 12px; background: rgba(0,0,0,0.55); color: white; font-size: 11px; padding: 2px 8px; border-radius: 6px;">
                        <span x-text="currentIndex + 1"></span> / <span x-text="filteredImages.length"></span>
                    </div>
                </div>

                {{-- カテゴリフィルター --}}
                <div>
                    <select x-model="imageCategory" x-on:change="currentIndex = 0; thumbPage = 0;" style="background: #111; border: 1px solid #2a2a2a; border-radius: 3px; padding: 6px 28px 6px 10px; font-size: 12px; color: #ccc; outline: none; appearance: none; cursor: pointer;">
                        <option value="all">すべて</option>
                        <option value="exterior">外装</option>
                        <option value="interior">内装</option>
                        <option value="engine">エンジン</option>
                        <option value="other">その他</option>
                    </select>
                </div>

                {{-- サムネイル --}}
                <div style="display: flex; align-items: center; gap: 6px;">
                    <button x-on:click="thumbPage = Math.max(0, thumbPage - 1)" x-bind:disabled="thumbPage === 0" style="background: #dc5078; border: none; border-radius: 6px; width: 28px; height: 44px; color: #fff; font-size: 16px; cursor: pointer; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">‹</button>
                    <div style="flex: 1; display: grid; grid-template-columns: repeat(10, 1fr); gap: 4px;">
                        <template x-for="(img, idx) in thumbImages" :key="img.id">
                            <div x-on:click="selectThumb(idx)" style="aspect-ratio: 4/3; border-radius: 4px; overflow: hidden; cursor: pointer; border: 2px solid transparent;" :style="isCurrentThumb(idx) ? 'border-color: #dc5078;' : 'border-color: transparent;'">
                                <img :src="img.url" style="width: 100%; height: 100%; object-fit: cover;" />
                            </div>
                        </template>
                    </div>
                    <button x-on:click="thumbPage = Math.min(totalThumbPages - 1, thumbPage + 1)" x-bind:disabled="thumbPage >= totalThumbPages - 1" style="background: #dc5078; border: none; border-radius: 6px; width: 28px; height: 44px; color: #fff; font-size: 16px; cursor: pointer; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">›</button>
                </div>
            </div>

            {{-- 右：価格エリア（飾り） --}}
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 8px; padding: 20px;">
                    <div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #2a2a2a;">
                        <p style="font-size: 11px; color: #888; margin: 0 0 4px;">支払総額（税込）</p>
                        <p style="font-family: 'Cormorant Garamond', serif; font-size: 32px; color: #dc5078; margin: 0; line-height: 1;">
                            {{ number_format((int)$car->price / 10000, 1) }}万円
                        </p>
                    </div>
                    <div>
                        <p style="font-size: 11px; color: #888; margin: 0 0 4px;">車両本体価格（税込）</p>
                        <p style="font-family: 'Cormorant Garamond', serif; font-size: 24px; color: #ccc; margin: 0; line-height: 1;">
                            {{ number_format((int)$car->price / 10000, 1) }}万円
                        </p>
                    </div>

                    @if($loan)
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #2a2a2a;">
                        <p style="font-size: 11px; color: #dc5078; margin: 0 0 8px; font-weight: 500;">ローンご利用時</p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <div>
                                <p style="font-size: 10px; color: #666; margin: 0 0 2px;">通常ローン 月々</p>
                                <p style="font-size: 18px; color: #ccc; margin: 0; font-weight: 500;">お問い合わせ</p>
                            </div>
                            <div>
                                <p style="font-size: 10px; color: #666; margin: 0 0 2px;">実質金利</p>
                                <p style="font-size: 16px; color: #dc5078; margin: 0; font-weight: 500;">{{ $loan['interest_rate'] }}%</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- お気に入り・在庫確認ボタン（飾り） --}}
                <button disabled style="padding: 10px; border: 1px solid #333; border-radius: 4px; background: transparent; color: #555; font-size: 12px; cursor: not-allowed; opacity: 0.5;">
                    ♡ お気に入りに追加
                </button>
                <button disabled style="padding: 12px; border: none; border-radius: 4px; background: #dc5078; color: #fff; font-size: 13px; font-weight: 500; cursor: not-allowed; opacity: 0.5;">
                    在庫確認・見積依頼
                </button>

                {{-- 基本スペック --}}
                <div style="background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 8px; padding: 16px; display: flex; flex-direction: column; gap: 10px;">
                    @foreach([
                        ['年式',     $car->model_year ? $car->model_year . '年' : '-'],
                        ['走行距離', number_format($car->mileage) . 'km'],
                        ['修復歴',   $this->formatRepairHistory($car->repair_history)],
                        ['車検',     $this->formatInspection($detail?->inspection_status, $detail?->inspection_expire_date)],
                    ] as [$label, $value])
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 11px; color: #666;">{{ $label }}</span>
                        <span style="font-size: 12px; font-weight: 500; color: #ccc;">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ===== 車両の状態 ===== --}}
        <div style="margin-bottom: 48px; border-top: 1px solid #1a1a1a; padding-top: 32px;">
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 300; color: #fff; letter-spacing: 0.1em; margin: 0 0 24px; padding-left: 12px; border-left: 3px solid #dc5078;">車両の状態</h2>
            <div style="border: 1px solid #1a1a1a; border-radius: 4px; overflow: hidden;">
                @foreach([
                    ['年式',       $car->model_year ? $car->model_year . '年' : '-',  'ワンオーナー',      $this->hasOption('one_owner') ? 'あり' : '-'],
                    ['走行距離',   number_format($car->mileage) . 'km',                'キャンピングカー',  $this->hasOption('camping_car') ? 'あり' : '-'],
                    ['修復歴',     $this->formatRepairHistory($car->repair_history),   '福祉車両',          $this->hasOption('welfare_car') ? 'あり' : '-'],
                    ['車検',       $this->formatInspection($detail?->inspection_status, $detail?->inspection_expire_date), '登録済未使用車', $this->hasOption('unused') ? 'あり' : '-'],
                    ['色',         $car->color ?? '-',                                 'エコカー減税対象',  $this->hasOption('eco_car') ? 'あり' : '-'],
                ] as [$label1, $value1, $label2, $value2])
                <div style="display: grid; grid-template-columns: 1fr 2fr 1fr 2fr; border-bottom: 1px solid #1a1a1a;">
                    <div style="padding: 12px 16px; font-size: 11px; color: #666; background: #111;">{{ $label1 }}</div>
                    <div style="padding: 12px 16px; font-size: 13px; font-weight: 500; color: #ccc; background: #0d0d0d;">{{ $value1 }}</div>
                    <div style="padding: 12px 16px; font-size: 11px; color: #666; background: #111;">{{ $label2 }}</div>
                    <div style="padding: 12px 16px; font-size: 13px; font-weight: 500; color: #ccc; background: #0d0d0d;">{{ $value2 }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ===== 車両のスペック ===== --}}
        <div style="margin-bottom: 48px; border-top: 1px solid #1a1a1a; padding-top: 32px;">
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 300; color: #fff; letter-spacing: 0.1em; margin: 0 0 24px; padding-left: 12px; border-left: 3px solid #dc5078;">車両のスペック</h2>
            <div style="border: 1px solid #1a1a1a; border-radius: 4px; overflow: hidden;">
                @foreach([
                    ['ボディタイプ', $car->bodyType?->name ?? '-',                                              '駆動方式',   $detail?->drive_system ?? '-'],
                    ['ボディカラー', $car->color ?? '-',                                                        'ハンドル',   $this->formatSteering($detail?->steering_wheel)],
                    ['排気量',       $detail?->displacement ? number_format($detail->displacement) . 'cc' : '-', 'ミッション', $car->transmission ?? '-'],
                    ['エンジン種別', $this->formatFuelType($car->fuel_type),                                    '乗車定員',   $detail?->riding_capacity ? $detail->riding_capacity . '名' : '-'],
                    ['ドア数',       $detail?->number_of_doors ? $detail->number_of_doors . 'ドア' : '-',       'スライドドア', $this->formatSlideDoor($detail?->slide_door)],
                ] as [$label1, $value1, $label2, $value2])
                <div style="display: grid; grid-template-columns: 1fr 2fr 1fr 2fr; border-bottom: 1px solid #1a1a1a;">
                    <div style="padding: 12px 16px; font-size: 11px; color: #666; background: #111;">{{ $label1 }}</div>
                    <div style="padding: 12px 16px; font-size: 13px; font-weight: 500; color: #ccc; background: #0d0d0d;">{{ $value1 }}</div>
                    <div style="padding: 12px 16px; font-size: 11px; color: #666; background: #111;">{{ $label2 }}</div>
                    <div style="padding: 12px 16px; font-size: 13px; font-weight: 500; color: #ccc; background: #0d0d0d;">{{ $value2 }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ===== カタログスペック ===== --}}
        @if($vehicleSpec)
        <div style="margin-bottom: 48px; border-top: 1px solid #1a1a1a; padding-top: 32px;">
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 300; color: #fff; letter-spacing: 0.1em; margin: 0 0 24px; padding-left: 12px; border-left: 3px solid #dc5078;">カタログスペック</h2>
            <div style="border: 1px solid #1a1a1a; border-radius: 4px; overflow: hidden;">
                @php
                    $v  = $vehicleSpec['vehicle'];
                    $vv = $vehicleSpec['vehicleVersion'];
                @endphp
                @foreach([
                    ['型式名',   $v?->name ?? '-',                                                                                                                                    'モデルコード', $v?->model_code ?? '-'],
                    ['排気量',   $vv?->displacement_cc ? number_format($vv->displacement_cc) . 'cc' : '-',                                                                           '駆動方式',     $vv?->drive_type ?? '-'],
                    ['ミッション', $vv?->transmission_type ?? '-',                                                                                                                   '車両重量',     $vv?->weight_kg ? number_format($vv->weight_kg) . 'kg' : '-'],
                    ['燃費',     $vv?->fuel_efficiency_from && $vv?->fuel_efficiency_to ? "{$vv->fuel_efficiency_from}〜{$vv->fuel_efficiency_to}km/L" : ($vv?->fuel_efficiency_from ? "{$vv->fuel_efficiency_from}km/L" : '-'), '最高出力', $vv?->max_power_kw ? $vv->max_power_kw . 'kW' : '-'],
                ] as [$label1, $value1, $label2, $value2])
                <div style="display: grid; grid-template-columns: 1fr 2fr 1fr 2fr; border-bottom: 1px solid #1a1a1a;">
                    <div style="padding: 12px 16px; font-size: 11px; color: #666; background: #111;">{{ $label1 }}</div>
                    <div style="padding: 12px 16px; font-size: 13px; font-weight: 500; color: #ccc; background: #0d0d0d;">{{ $value1 }}</div>
                    <div style="padding: 12px 16px; font-size: 11px; color: #666; background: #111;">{{ $label2 }}</div>
                    <div style="padding: 12px 16px; font-size: 13px; font-weight: 500; color: #ccc; background: #0d0d0d;">{{ $value2 }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ===== 装備仕様 ===== --}}
        <div style="border-top: 1px solid #1a1a1a; padding-top: 32px;">
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 300; color: #fff; letter-spacing: 0.1em; margin: 0 0 24px; padding-left: 12px; border-left: 3px solid #dc5078;">装備仕様</h2>
            @foreach($equipSections as $section)
            <div style="margin-bottom: 8px; border: 1px solid #1a1a1a; border-radius: 4px; overflow: hidden;">
                <div
                    x-on:click="toggleSection('{{ $section['key'] }}')"
                    style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; background: #111; cursor: pointer; font-size: 13px; font-weight: 500; color: #ccc; letter-spacing: 0.05em;"
                >
                    <span>{{ $section['label'] }}</span>
                    <span x-text="openSections.includes('{{ $section['key'] }}') ? '−' : '+'"></span>
                </div>
                <div
                    x-show="openSections.includes('{{ $section['key'] }}')"
                    x-cloak
                    style="padding: 16px; background: #0d0d0d;"
                >
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;">
                        @foreach($section['items'] as $item)
                        <div style="padding: 8px 12px; border-radius: 3px; font-size: 11px; letter-spacing: 0.03em;
                            {{ $item['is_equipped']
                                ? 'background: rgba(220,80,120,0.1); color: #dc5078; border: 1px solid rgba(220,80,120,0.3);'
                                : 'background: #1a1a1a; color: #444; border: 1px solid #1a1a1a;'
                            }}">
                            {{ $item['label'] }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">

</x-filament-panels::page>