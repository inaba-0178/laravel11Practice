<x-filament-panels::page>
    <div class="space-y-6">

        {{-- 問い合わせ情報 --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-x-3 px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                    問い合わせ情報
                </h3>
                <span class="fi-badge rounded-md px-2 py-1 text-xs font-medium
                    {{ $this->record->status === 'new'           ? 'bg-warning-100 text-warning-700' : '' }}
                    {{ $this->record->status === 'draft'         ? 'bg-info-100 text-info-700' : '' }}
                    {{ $this->record->status === 'replied'       ? 'bg-success-100 text-success-700' : '' }}
                    {{ $this->record->status === 'phone_replied' ? 'bg-primary-100 text-primary-700' : '' }}
                ">
                    {{ $this->getStatusLabel() }}
                </span>
            </div>
            <div class="px-6 py-4 grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">種別</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->getInquiryTypeLabel() }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">受信日時</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->record->created_at->format('Y/m/d H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">お名前</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->getSenderName() }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">返信先メール</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->getReplyToEmail() ?? '未登録' }}
                    </p>
                </div>
                @if($this->record->phone)
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">電話番号</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->record->phone }}
                    </p>
                </div>
                @endif
                @if($this->record->postal_code)
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">郵便番号</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        〒{{ $this->record->postal_code }}
                    </p>
                </div>
                @endif
                @if($this->record->address)
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">住所</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->record->address }}
                    </p>
                </div>
                @endif
                @if($this->record->car)
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">対象車両</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $this->record->car->series?->series_name ?? "車両ID: {$this->record->car_id}" }}
                    </p>
                </div>
                @endif
                @if($this->record->message)
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">お問い合わせ内容</p>
                    <p class="text-sm text-gray-900 dark:text-white mt-1 whitespace-pre-wrap bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        {{ $this->record->message }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- 見積作成ボタン --}}
        <div class="flex justify-end">
            <button
                wire:click="$set('showEstimateForm', {{ $this->showEstimateForm ? 'false' : 'true' }})"
                class="fi-btn relative inline-grid items-center justify-center gap-1.5 font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg px-4 py-2 text-sm border border-primary-600 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20"
            >
                {{ $this->showEstimateForm ? '▲ 見積フォームを閉じる' : '▼ 見積書を作成する' }}
            </button>
        </div>

        {{-- 見積フォーム --}}
        @if($this->showEstimateForm)
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                    見積書作成
                </h3>
            </div>
            <div class="px-6 py-4 space-y-6">

                {{-- 顧客情報 --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                        顧客情報
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">お名前</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $this->estimateForm['customer_name'] ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">ニックネーム</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $this->estimateForm['customer_nickname'] ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">電話番号</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $this->estimateForm['customer_phone'] ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">郵便番号</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $this->estimateForm['customer_postal_code'] ?? '-' }}
                            </p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500 dark:text-gray-400">住所</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $this->estimateForm['customer_address'] ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">生年月日</label>
                            <input type="date" wire:model="estimateForm.customer_birth_date"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">連絡先Tel</label>
                            <input type="text" wire:model="estimateForm.customer_contact_phone"
                                placeholder="090-1234-5678"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500 dark:text-gray-400">勤務先等</label>
                            <input type="text" wire:model="estimateForm.customer_workplace"
                                placeholder="株式会社〇〇"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                    </div>
                </div>

                {{-- 車両情報 --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                        車両情報
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">型式</label>
                            <input type="text" wire:model="estimateForm.vehicle_model"
                                placeholder="例：ZWE211H"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">車台番号</label>
                            <input type="text" wire:model="estimateForm.chassis_number"
                                placeholder="例：ZWE211-1234567"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">登録番号</label>
                            <input type="text" wire:model="estimateForm.registration_number"
                                placeholder="例：品川500あ1234"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">記録簿</label>
                            <div class="mt-2 flex gap-4">
                                <label class="flex items-center gap-2 text-sm text-gray-900 dark:text-white cursor-pointer">
                                    <input type="radio" wire:model="estimateForm.has_service_record" value="1" />
                                    有
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-900 dark:text-white cursor-pointer">
                                    <input type="radio" wire:model="estimateForm.has_service_record" value="0" />
                                    無
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 価格情報 --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                        価格情報
                    </h4>

                    {{-- 自動取得項目サマリー --}}
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">車両本体価格</span>
                            <span class="text-gray-900 dark:text-white">{{ number_format($this->estimateForm['vehicle_price']) }}円</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">リサイクル料金</span>
                            <span class="text-gray-900 dark:text-white">{{ number_format($this->estimateForm['recycle_fee']) }}円</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">自動車税</span>
                            <span class="text-gray-900 dark:text-white">{{ number_format($this->estimateForm['vehicle_tax']) }}円</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">重量税</span>
                            <span class="text-gray-900 dark:text-white">{{ number_format($this->estimateForm['weight_tax']) }}円</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">自賠責保険料</span>
                            <span class="text-gray-900 dark:text-white">{{ number_format($this->estimateForm['liability_insurance']) }}円</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">登録費用</span>
                            <span class="text-gray-900 dark:text-white">{{ number_format($this->estimateForm['registration_fee']) }}円</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">車庫証明手続費用</span>
                            <span class="text-gray-900 dark:text-white">{{ number_format($this->estimateForm['garage_cert_fee']) }}円</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">納車費用</span>
                            <span class="text-gray-900 dark:text-white">{{ number_format($this->estimateForm['delivery_fee']) }}円</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">整備費用</span>
                            <span class="text-gray-900 dark:text-white">{{ number_format($this->estimateForm['maintenance_fee']) }}円</span>
                        </div>
                    </div>

                    {{-- 金額調整トグル --}}
                    <button
                        wire:click="$set('showPriceAdjust', {{ $this->showPriceAdjust ? 'false' : 'true' }})"
                        class="text-xs text-primary-600 hover:text-primary-500 font-medium mb-4 block"
                    >
                        {{ $this->showPriceAdjust ? '▲ 金額調整を閉じる' : '▼ 金額を調整する' }}
                    </button>

                    {{-- 金額調整フォーム --}}
                    @if($this->showPriceAdjust)
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">車両本体価格（円）</label>
                            <input type="number" wire:model.live="estimateForm.vehicle_price"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">リサイクル料金（円）</label>
                            <input type="number" wire:model.live="estimateForm.recycle_fee"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">自動車税（円）</label>
                            <input type="number" wire:model.live="estimateForm.vehicle_tax"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">重量税（円）</label>
                            <input type="number" wire:model.live="estimateForm.weight_tax"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">自賠責保険料（円）</label>
                            <input type="number" wire:model.live="estimateForm.liability_insurance"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">登録費用（円）</label>
                            <input type="number" wire:model.live="estimateForm.registration_fee"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">車庫証明手続費用（円）</label>
                            <input type="number" wire:model.live="estimateForm.garage_cert_fee"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">納車費用（円）</label>
                            <input type="number" wire:model.live="estimateForm.delivery_fee"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">整備費用（円）</label>
                            <input type="number" wire:model.live="estimateForm.maintenance_fee"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                    </div>
                    @endif

                    {{-- 値引き・有効期限 --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500 dark:text-gray-400 mb-1 block">値引き種別</label>
                            <div class="flex gap-2 mb-2">
                                <button
                                    wire:click="$set('discountType', 'tax_excluded')"
                                    class="px-3 py-1 text-xs rounded-lg border transition-all
                                        {{ $this->discountType === 'tax_excluded'
                                            ? 'bg-primary-600 text-white border-primary-600'
                                            : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600' }}"
                                >
                                    税抜き値引き
                                </button>
                                <button
                                    wire:click="$set('discountType', 'tax_included')"
                                    class="px-3 py-1 text-xs rounded-lg border transition-all
                                        {{ $this->discountType === 'tax_included'
                                            ? 'bg-primary-600 text-white border-primary-600'
                                            : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600' }}"
                                >
                                    税込み値引き
                                </button>
                            </div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">値引き（円）</label>
                            <input type="number" wire:model.live="estimateForm.discount"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                            <p class="text-xs text-gray-400 mt-1">
                                @if($this->discountType === 'tax_excluded')
                                    税抜き値引き：{{ number_format((int)($this->estimateForm['discount'] ?? 0)) }}円
                                    （税込換算：{{ number_format((int)round((float)($this->estimateForm['discount'] ?? 0) * (1 + \App\Domain\Common\Constants\TaxConstants::CONSUMPTION_TAX_RATE))) }}円）
                                @else
                                    税込み値引き：{{ number_format((int)($this->estimateForm['discount'] ?? 0)) }}円（総額から直接引きます）
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">有効期限</label>
                            <input type="date" wire:model="estimateForm.valid_until"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                    </div>

                    {{-- 合計金額プレビュー --}}
                    <div class="mt-4 flex justify-end">
                        <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-700 rounded-lg px-6 py-3">
                            <span class="text-sm text-primary-700 dark:text-primary-400 font-medium">
                                お見積金額：
                            </span>
                            <span class="text-xl font-bold text-primary-700 dark:text-primary-400">
                                {{ number_format($this->getEstimateTotal()) }}円
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 諸費用追加項目 --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                        諸費用（追加）
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">環境性能割（円）</label>
                            <input type="number" wire:model.live="estimateForm.environmental_performance_tax"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">検査/登録/届出・課税（円）</label>
                            <input type="number" wire:model.live="estimateForm.inspection_registration_fee"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">検査/登録/届出・非課税（円）</label>
                            <input type="number" wire:model.live="estimateForm.inspection_registration_fee_exempt"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">下取車諸手続き（円）</label>
                            <input type="number" wire:model.live="estimateForm.trade_in_handling_fee"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">査定料（円）</label>
                            <input type="number" wire:model.live="estimateForm.assessment_fee"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                    </div>
                </div>

                {{-- 下取車情報 --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                        下取車情報
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500 dark:text-gray-400">車名（型式等）</label>
                            <input type="text" wire:model="estimateForm.trade_in_name"
                                placeholder="例：アクセラ"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">年式</label>
                            <input type="text" wire:model="estimateForm.trade_in_model_year"
                                placeholder="例：H30"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">車検日</label>
                            <input type="date" wire:model="estimateForm.trade_in_inspection_date"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">走行距離（km）</label>
                            <input type="number" wire:model="estimateForm.trade_in_mileage"
                                placeholder="例：98520"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">車体色</label>
                            <input type="text" wire:model="estimateForm.trade_in_color"
                                placeholder="例：シルバーM"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">下取価格（円）</label>
                            <input type="number" wire:model.live="estimateForm.trade_in_price"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                    </div>
                </div>

                {{-- 支払い情報 --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                        支払い情報
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">頭金/現金/他（円）</label>
                            <input type="number" wire:model.live="estimateForm.down_payment"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">残金/所要資金（円）</label>
                            <input type="number" wire:model.live="estimateForm.remaining_amount"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">支払回数</label>
                            <input type="number" wire:model="estimateForm.credit_months"
                                placeholder="例：60"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">分割手数料（円）</label>
                            <input type="number" wire:model="estimateForm.credit_fee"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">月払（円）</label>
                            <input type="number" wire:model="estimateForm.monthly_payment"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400">賞与払（円）</label>
                            <input type="number" wire:model="estimateForm.bonus_payment"
                                class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                        </div>
                    </div>
                </div>

                {{-- 付属品 --}}
                <div>
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            付属品
                        </h4>
                        <button wire:click="addAccessory"
                            class="text-xs text-primary-600 hover:text-primary-500 font-medium">
                            ＋ 付属品を追加
                        </button>
                    </div>
                    @if(count($this->accessories) > 0)
                    <div class="space-y-2">
                        @foreach($this->accessories as $index => $accessory)
                        <div class="flex items-center gap-3">
                            <input type="text"
                                wire:model="accessories.{{ $index }}.name"
                                placeholder="例：フロアマット"
                                class="flex-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                            <input type="number"
                                wire:model="accessories.{{ $index }}.price"
                                placeholder="金額"
                                class="w-32 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                            <button wire:click="removeAccessory({{ $index }})"
                                class="text-danger-600 hover:text-danger-500 text-sm font-medium">
                                削除
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-gray-400 dark:text-gray-500">付属品はありません</p>
                    @endif
                </div>

                {{-- 必要書類 --}}
                <div>
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            必要書類
                        </h4>
                        <button wire:click="addDocument"
                            class="text-xs text-primary-600 hover:text-primary-500 font-medium">
                            ＋ 書類を追加
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($this->documents as $index => $document)
                        <div class="flex items-center gap-2">
                            <input type="text"
                                wire:model="documents.{{ $index }}.name"
                                class="flex-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                            <button wire:click="removeDocument({{ $index }})"
                                class="text-danger-600 hover:text-danger-500 text-sm font-medium flex-shrink-0">
                                削除
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- 備考 --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                        備考
                    </h4>
                    <textarea wire:model="estimateForm.notes" rows="3"
                        placeholder="備考があれば入力してください"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </textarea>
                </div>

                {{-- 作成ボタン --}}
                <div class="flex justify-end">
                    <button
                        wire:click="createEstimate"
                        wire:confirm="見積書を作成してダウンロードしますか？"
                        class="fi-btn relative inline-grid items-center justify-center gap-1.5 font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg px-4 py-2 text-sm bg-primary-600 text-white hover:bg-primary-500"
                    >
                        見積書を作成してダウンロード
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- 対応エリア --}}
        @if(!in_array($this->record->status, ['replied', 'phone_replied']))
            @if($this->hasEmail())
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                    <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                        返答を作成
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        返信先：{{ $this->getReplyToEmail() }}
                    </p>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <textarea wire:model="replyText" rows="8"
                        placeholder="返答内容を入力してください"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </textarea>
                    <div class="flex items-center gap-3 justify-end">
                        <button wire:click="saveDraft"
                            class="fi-btn relative inline-grid items-center justify-center gap-1.5 font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                            一時保存
                        </button>
                        <button wire:click="sendReply"
                            wire:confirm="この内容で送信してよろしいですか？"
                            class="fi-btn relative inline-grid items-center justify-center gap-1.5 font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg px-3 py-2 text-sm bg-primary-600 text-white hover:bg-primary-500">
                            送信する
                        </button>
                    </div>
                </div>
            </div>
            @else
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                    <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                        電話対応
                    </h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="flex items-start gap-3 rounded-lg bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700 p-4">
                        <svg class="w-5 h-5 text-warning-600 dark:text-warning-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-warning-700 dark:text-warning-400">
                                メールアドレスが未登録のため、メールでの返答ができません。
                            </p>
                            <p class="text-xs text-warning-600 dark:text-warning-300 mt-1">
                                電話番号：{{ $this->record->phone ?? '-' }} にて対応をお願いします。
                            </p>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            電話対応メモ
                        </label>
                        <textarea wire:model="phoneMemo" rows="6"
                            placeholder="電話対応の内容をメモしてください"
                            class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </textarea>
                    </div>
                    <div class="flex justify-end">
                        <button wire:click="savePhoneMemo"
                            wire:confirm="電話対応済みとして保存しますか？"
                            class="fi-btn relative inline-grid items-center justify-center gap-1.5 font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg px-3 py-2 text-sm bg-primary-600 text-white hover:bg-primary-500">
                            電話対応済みとして保存
                        </button>
                    </div>
                </div>
            </div>
            @endif
        @endif

        {{-- 返信済みの場合 --}}
        @if($this->record->status === 'replied' && $this->record->reply)
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                    返答内容
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $this->record->replied_at?->format('Y/m/d H:i') }} に送信済み
                </p>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    {{ $this->record->reply }}
                </p>
            </div>
        </div>
        @endif

        {{-- 電話対応済みの場合 --}}
        @if($this->record->status === 'phone_replied' && $this->record->phone_memo)
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                    電話対応メモ
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $this->record->replied_at?->format('Y/m/d H:i') }} に対応済み
                </p>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    {{ $this->record->phone_memo }}
                </p>
            </div>
        </div>
        @endif

    </div>
</x-filament-panels::page>