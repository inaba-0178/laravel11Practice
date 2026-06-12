<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ===== ドキュメント種別選択 ===== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                    種別選択
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="flex gap-3">
                    <button
                        wire:click="$set('documentType', 'estimate')"
                        class="px-6 py-3 text-sm font-semibold rounded-lg border-2 transition-all
                            {{ $this->documentType === 'estimate'
                                ? 'bg-primary-600 text-white border-primary-600'
                                : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600' }}">
                        📄 見積書
                    </button>
                    <button
                        wire:click="$set('documentType', 'contract')"
                        class="px-6 py-3 text-sm font-semibold rounded-lg border-2 transition-all
                            {{ $this->documentType === 'contract'
                                ? 'bg-success-600 text-white border-success-600'
                                : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600' }}">
                        📝 契約書
                    </button>
                </div>
                @if($this->documentType === 'contract')
                <div class="mt-3 text-xs text-warning-600 dark:text-warning-400 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700 rounded-lg px-4 py-2">
                    契約書として出力されます。署名捺印欄・約款（裏面）が追加されます。
                </div>
                @endif
            </div>
        </div>

        {{-- ===== 車両情報（表示のみ） ===== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                    車両情報
                </h3>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">メーカー</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->record->manufacturer?->name ?? '-' }}
                    </p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">車名</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->record->series?->series_name ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">年式</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->record->model_year ? $this->record->model_year . '年' : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">排気量</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->record->detail?->displacement ? number_format($this->record->detail->displacement) . 'cc' : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">走行距離</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ number_format($this->record->mileage) }}km
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">車体色</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->record->color ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">在庫番号</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->record->stock_number ?? 'STK-' . $this->record->id }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">車両本体価格</p>
                    <p class="text-sm font-bold text-primary-600 dark:text-primary-400 mt-1">
                        {{ number_format($this->record->price) }}円
                    </p>
                </div>
            </div>
        </div>

        {{-- ===== 顧客情報 ===== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                    顧客情報
                </h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">お名前 <span class="text-danger-600">*</span></label>
                        <input type="text" wire:model="customerName"
                            placeholder="例：山田 太郎"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">ふりがな</label>
                        <input type="text" wire:model="customerNickname"
                            placeholder="例：やまだ たろう"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">電話番号</label>
                        <input type="text" wire:model="customerPhone"
                            placeholder="例：090-1234-5678"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">連絡先Tel</label>
                        <input type="text" wire:model="customerContactPhone"
                            placeholder="例：03-1234-5678"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">郵便番号</label>
                        <div class="flex gap-2 mt-1" x-data>
                            <input type="text" wire:model="customerPostalCode"
                                placeholder="例：1234567"
                                maxlength="8"
                                class="w-32 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                            <button
                                type="button"
                                @click="
                                    const code = $wire.customerPostalCode.replace('-','');
                                    if(code.length !== 7){ return; }
                                    fetch('https://zipcloud.ibsnet.co.jp/api/search?zipcode=' + code)
                                        .then(r => r.json())
                                        .then(data => {
                                            if(data.results){
                                                const r = data.results[0];
                                                $wire.customerAddress = r.address1 + r.address2 + r.address3;
                                            }
                                        });
                                "
                                class="px-3 py-2 text-xs rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600">
                                住所を検索
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">生年月日</label>
                        <input type="date" wire:model="customerBirthDate"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs text-gray-500 dark:text-gray-400">住所</label>
                        <input type="text" wire:model="customerAddress"
                            placeholder="例：東京都港区東青山1-2-3"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs text-gray-500 dark:text-gray-400">勤務先等</label>
                        <input type="text" wire:model="customerWorkplace"
                            placeholder="例：株式会社〇〇"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== 車両詳細情報 ===== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                    車両詳細情報
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">型式</label>
                        <input type="text" wire:model="vehicleModel"
                            placeholder="例：ZWE211H"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">車台番号</label>
                        <input type="text" wire:model="chassisNumber"
                            placeholder="例：ZWE211-1234567"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">登録番号</label>
                        <input type="text" wire:model="registrationNumber"
                            placeholder="例：品川500あ1234"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">記録簿</label>
                        <div class="mt-2 flex gap-4">
                            <label class="flex items-center gap-2 text-sm text-gray-900 dark:text-white cursor-pointer">
                                <input type="radio" wire:model="hasServiceRecord" value="1" /> 有
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-900 dark:text-white cursor-pointer">
                                <input type="radio" wire:model="hasServiceRecord" value="0" /> 無
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== 価格情報 ===== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                    価格情報
                </h3>
            </div>
            <div class="px-6 py-4 space-y-4">

                {{-- 自動取得項目サマリー --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">車両本体価格</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ number_format($this->vehiclePrice) }}円</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">リサイクル料金</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($this->recycleFee) }}円</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">自動車税</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($this->vehicleTax) }}円</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">重量税</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($this->weightTax) }}円</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">自賠責保険料</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($this->liabilityInsurance) }}円</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">登録費用</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($this->registrationFee) }}円</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">車庫証明手続費用</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($this->garageCertFee) }}円</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">納車費用</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($this->deliveryFee) }}円</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">整備費用</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($this->maintenanceFee) }}円</span>
                    </div>
                </div>

                <button
                    wire:click="$set('showPriceAdjust', {{ $this->showPriceAdjust ? 'false' : 'true' }})"
                    class="text-xs text-primary-600 hover:text-primary-500 font-medium block">
                    {{ $this->showPriceAdjust ? '▲ 金額調整を閉じる' : '▼ 金額を調整する' }}
                </button>

                @if($this->showPriceAdjust)
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">車両本体価格（円）</label>
                        <input type="number" wire:model.live="vehiclePrice" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">リサイクル料金（円）</label>
                        <input type="number" wire:model.live="recycleFee" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">自動車税（円）</label>
                        <input type="number" wire:model.live="vehicleTax" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">重量税（円）</label>
                        <input type="number" wire:model.live="weightTax" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">自賠責保険料（円）</label>
                        <input type="number" wire:model.live="liabilityInsurance" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">登録費用（円）</label>
                        <input type="number" wire:model.live="registrationFee" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">車庫証明手続費用（円）</label>
                        <input type="number" wire:model.live="garageCertFee" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">納車費用（円）</label>
                        <input type="number" wire:model.live="deliveryFee" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">整備費用（円）</label>
                        <input type="number" wire:model.live="maintenanceFee" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                </div>
                @endif

                {{-- 値引き --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-xs text-gray-500 dark:text-gray-400 mb-1 block">値引き種別</label>
                        <div class="flex gap-2 mb-2">
                            <button wire:click="$set('discountType', 'tax_excluded')"
                                class="px-3 py-1 text-xs rounded-lg border transition-all {{ $this->discountType === 'tax_excluded' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600' }}">
                                税抜き値引き
                            </button>
                            <button wire:click="$set('discountType', 'tax_included')"
                                class="px-3 py-1 text-xs rounded-lg border transition-all {{ $this->discountType === 'tax_included' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600' }}">
                                税込み値引き
                            </button>
                        </div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">値引き（円）</label>
                        <input type="number" wire:model.live="discount"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">有効期限</label>
                        <input type="date" wire:model="validUntil"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                </div>

                {{-- 合計金額プレビュー --}}
                <div class="mt-4 flex justify-end">
                    <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-700 rounded-lg px-6 py-3">
                        <span class="text-sm text-primary-700 dark:text-primary-400 font-medium">お見積金額：</span>
                        <span class="text-xl font-bold text-primary-700 dark:text-primary-400">
                            {{ number_format($this->getEstimateTotal()) }}円
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== 諸費用追加項目 ===== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">諸費用（追加）</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">環境性能割（円）</label>
                        <input type="number" wire:model.live="environmentalPerformanceTax" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">検査/登録/届出・課税（円）</label>
                        <input type="number" wire:model.live="inspectionRegistrationFee" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">検査/登録/届出・非課税（円）</label>
                        <input type="number" wire:model.live="inspectionRegistrationFeeExempt" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">下取車諸手続き（円）</label>
                        <input type="number" wire:model.live="tradeInHandlingFee" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">査定料（円）</label>
                        <input type="number" wire:model.live="assessmentFee" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== 下取車情報 ===== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">下取車情報</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label class="text-xs text-gray-500 dark:text-gray-400">車名（型式等）</label>
                        <input type="text" wire:model="tradeInName" placeholder="例：アクセラ" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">年式</label>
                        <input type="text" wire:model="tradeInModelYear" placeholder="例：H30" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">車検日</label>
                        <input type="date" wire:model="tradeInInspectionDate" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">走行距離（km）</label>
                        <input type="number" wire:model="tradeInMileage" placeholder="例：98520" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">車体色</label>
                        <input type="text" wire:model="tradeInColor" placeholder="例：シルバーM" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">下取価格（円）</label>
                        <input type="number" wire:model.live="tradeInPrice" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== 支払い情報 ===== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">支払い情報</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">頭金/現金/他（円）</label>
                        <input type="number" wire:model.live="downPayment" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">残金/所要資金（円）</label>
                        <input type="number" wire:model.live="remainingAmount" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">支払回数</label>
                        <input type="number" wire:model="creditMonths" placeholder="例：60" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">分割手数料（円）</label>
                        <input type="number" wire:model="creditFee" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">月払（円）</label>
                        <input type="number" wire:model="monthlyPayment" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 dark:text-gray-400">賞与払（円）</label>
                        <input type="number" wire:model="bonusPayment" class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== 付属品 ===== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">付属品</h3>
                <button wire:click="addAccessory" class="text-xs text-primary-600 hover:text-primary-500 font-medium">＋ 付属品を追加</button>
            </div>
            <div class="px-6 py-4">
                @if(count($this->accessories) > 0)
                <div class="space-y-2">
                    @foreach($this->accessories as $index => $accessory)
                    <div class="flex items-center gap-3">
                        <input type="text" wire:model="accessories.{{ $index }}.name" placeholder="例：フロアマット"
                            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        <input type="number" wire:model="accessories.{{ $index }}.price" placeholder="金額"
                            class="w-32 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        <button wire:click="removeAccessory({{ $index }})" class="text-danger-600 hover:text-danger-500 text-sm font-medium">削除</button>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-gray-400 dark:text-gray-500">付属品はありません</p>
                @endif
            </div>
        </div>

        {{-- ===== 必要書類 ===== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">必要書類</h3>
                <button wire:click="addDocument" class="text-xs text-primary-600 hover:text-primary-500 font-medium">＋ 書類を追加</button>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-2 gap-2">
                    @foreach($this->documents as $index => $document)
                    <div class="flex items-center gap-2">
                        <input type="text" wire:model="documents.{{ $index }}.name"
                            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        <button wire:click="removeDocument({{ $index }})" class="text-danger-600 hover:text-danger-500 text-sm font-medium flex-shrink-0">削除</button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ===== 備考 ===== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">備考</h3>
            </div>
            <div class="px-6 py-4">
                <textarea wire:model="notes" rows="3" placeholder="備考があれば入力してください"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </textarea>
            </div>
        </div>

        {{-- ===== 作成ボタン ===== --}}
        <div class="flex items-center justify-between">
            <a href="{{ $this->getBackUrl() }}"
                class="fi-btn relative inline-grid items-center justify-center gap-1.5 font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                ← 車両詳細に戻る
            </a>
            <button
                wire:click="createDocument"
                wire:confirm="{{ $this->documentType === 'contract' ? '契約書を作成してダウンロードしますか？' : '見積書を作成してダウンロードしますか？' }}"
                class="fi-btn relative inline-grid items-center justify-center gap-1.5 font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg px-6 py-2 text-sm {{ $this->documentType === 'contract' ? 'bg-success-600 hover:bg-success-500' : 'bg-primary-600 hover:bg-primary-500' }} text-white">
                {{ $this->documentType === 'contract' ? '📝 契約書を作成してダウンロード' : '📄 見積書を作成してダウンロード' }}
            </button>
        </div>

    </div>
</x-filament-panels::page>