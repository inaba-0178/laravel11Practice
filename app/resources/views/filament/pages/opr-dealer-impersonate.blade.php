<x-filament-panels::page>
    @php
        $dealers          = $this->getDealers();
        $areas            = $this->getAreas();
        $filteredRegions  = $this->getFilteredRegions();
        $isImpersonating  = $this->isImpersonating();
        $impersonatedName = $this->impersonatedDealerName();
    @endphp

    @once
    @push('styles')
    <style>
        .imp-banner {
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(220,80,120,0.15); border: 1px solid rgba(220,80,120,0.4);
            border-radius: 10px; padding: 12px 20px; margin-bottom: 16px;
        }
        .imp-banner__label { font-size: 12px; color: #dc5078; font-weight: 600; letter-spacing: 0.05em; }
        .imp-banner__name  { font-size: 15px; color: #fff; font-weight: 500; margin-top: 2px; }

        .imp-search { background: #111827; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; }
        .imp-search__row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 14px; }
        .imp-search__label { font-size: 11px; color: #9ca3af; display: block; margin-bottom: 5px; }
        .imp-search__select, .imp-search__input {
            width: 100%; height: 38px; padding: 0 10px; font-size: 13px;
            border: 1px solid rgba(255,255,255,0.1); border-radius: 6px;
            background: #0f172a; color: #fff; outline: none; box-sizing: border-box;
        }
        .imp-search__select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            padding-right: 32px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
        }
        .imp-search__select:disabled { opacity: 0.35; cursor: not-allowed; }
        .imp-search__actions { display: flex; justify-content: flex-end; gap: 10px; }
        .imp-search__clear {
            font-size: 12px; color: #6b7280; background: transparent;
            border: 1px solid rgba(255,255,255,0.1); border-radius: 6px;
            padding: 8px 20px; cursor: pointer; transition: border-color 0.2s, color 0.2s;
        }
        .imp-search__clear:hover { border-color: #4b5563; color: #d1d5db; }
        .imp-search__submit {
            font-size: 12px; color: #fff; background: #dc5078;
            border: none; border-radius: 6px; padding: 8px 24px;
            cursor: pointer; transition: opacity 0.2s;
        }
        .imp-search__submit:hover { opacity: 0.85; }

        .imp-count { font-size: 13px; color: #6b7280; margin-bottom: 14px; }
        .imp-count strong { font-size: 22px; color: #fff; margin-right: 3px; }

        .imp-dealer-list { display: flex; flex-direction: column; gap: 10px; }
        .imp-dealer-card {
            display: flex; align-items: center; gap: 16px;
            background: #111827; border: 1px solid rgba(255,255,255,0.07);
            border-radius: 10px; padding: 16px 20px; cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }
        .imp-dealer-card:hover { border-color: #dc5078; background: rgba(220,80,120,0.06); }
        .imp-dealer-card--active { border-color: #dc5078; background: rgba(220,80,120,0.1); }
        .imp-dealer-card__name { font-size: 15px; font-weight: 500; color: #fff; }
        .imp-dealer-card__address { font-size: 12px; color: #9ca3af; margin-top: 3px; }
        .imp-dealer-card__badge {
            margin-left: auto; font-size: 11px; color: #dc5078;
            background: rgba(220,80,120,0.15); border: 1px solid rgba(220,80,120,0.3);
            border-radius: 999px; padding: 3px 10px; white-space: nowrap; flex-shrink: 0;
        }

        .imp-modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 9998;
            display: flex; align-items: center; justify-content: center;
        }
        .imp-modal {
            background: #1f2937; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px; padding: 32px; width: 400px; max-width: 90vw;
            z-index: 9999;
        }
        .imp-modal__title { font-size: 18px; font-weight: 600; color: #fff; margin: 0 0 8px; }
        .imp-modal__sub   { font-size: 13px; color: #9ca3af; margin: 0 0 24px; }
        .imp-modal__name  { font-size: 20px; font-weight: 600; color: #dc5078; margin: 0 0 4px; }
        .imp-modal__addr  { font-size: 12px; color: #6b7280; margin: 0 0 24px; }
        .imp-modal__notice { font-size: 12px; color: #fbbf24; background: rgba(251,191,36,0.08); border: 1px solid rgba(251,191,36,0.2); border-radius: 8px; padding: 10px 14px; margin-bottom: 24px; }
        .imp-modal__actions { display: flex; justify-content: flex-end; gap: 10px; }
        .imp-modal__cancel {
            font-size: 13px; color: #9ca3af; background: transparent;
            border: 1px solid rgba(255,255,255,0.15); border-radius: 6px;
            padding: 9px 20px; cursor: pointer;
        }
        .imp-modal__ok {
            font-size: 13px; color: #fff; background: #dc5078;
            border: none; border-radius: 6px; padding: 9px 24px; cursor: pointer;
        }
    </style>
    @endpush
    @endonce

    {{-- なりすまし中バナー --}}
    @if ($isImpersonating)
        <div class="imp-banner">
            <div>
                <p class="imp-banner__label">ディーラー設定中</p>
                <p class="imp-banner__name">{{ $impersonatedName }}</p>
            </div>
            <button
                wire:click="clearImpersonate"
                wire:loading.attr="disabled"
                class="imp-search__clear"
                style="border-color: rgba(220,80,120,0.4); color: #dc5078;"
            >解除する</button>
        </div>
    @endif

    {{-- 検索フォーム --}}
    <div class="imp-search">
        <div class="imp-search__row">
            <div>
                <label class="imp-search__label">エリア</label>
                <select wire:model.live="searchArea" class="imp-search__select">
                    <option value="">選択してください</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area['id'] }}">{{ $area['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="imp-search__label">都道府県</label>
                <select wire:model.live="searchRegion" class="imp-search__select" @if(!$this->searchArea) disabled @endif>
                    <option value="">選択してください</option>
                    @foreach ($filteredRegions as $region)
                        <option value="{{ $region['id'] }}">{{ $region['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="imp-search__label">販売店名</label>
                <input
                    wire:model="searchName"
                    type="text"
                    placeholder="販売店名で探す"
                    class="imp-search__input"
                />
            </div>
        </div>
        <div class="imp-search__actions">
            <button wire:click="clearSearch" class="imp-search__clear">条件クリア</button>
            <button wire:click="search" class="imp-search__submit">検索する</button>
        </div>
    </div>

    {{-- 件数 --}}
    <p class="imp-count"><strong>{{ $dealers->total() }}</strong>店舗</p>

    {{-- ディーラー一覧 --}}
    <div class="imp-dealer-list">
        @forelse ($dealers as $dealer)
            @php
                $address  = collect([$dealer->city, $dealer->address_detail])->filter()->implode(' ');
                $isActive = $isImpersonating && \App\Services\ImpersonationService::getDealerId() === $dealer->id;
            @endphp
            <div
                class="imp-dealer-card {{ $isActive ? 'imp-dealer-card--active' : '' }}"
                wire:click="selectDealer({{ $dealer->id }}, '{{ addslashes($dealer->name) }}', '{{ addslashes($address) }}')"
            >
                <div>
                    <p class="imp-dealer-card__name">{{ $dealer->name }}</p>
                    @if ($address)
                        <p class="imp-dealer-card__address">{{ $address }}</p>
                    @endif
                </div>
                @if ($isActive)
                    <span class="imp-dealer-card__badge">設定中</span>
                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 48px; color: #4b5563; font-size: 13px;">
                該当する販売店が見つかりませんでした
            </div>
        @endforelse
    </div>

    {{-- ページネーション --}}
    @if ($dealers->hasPages())
        <div style="margin-top: 20px;">
            {{ $dealers->links() }}
        </div>
    @endif

    {{-- 確認モーダル --}}
    @if ($this->showModal)
        <div class="imp-modal-overlay" wire:click.self="$set('showModal', false)">
            <div class="imp-modal">
                <p class="imp-modal__title">ディーラー設定</p>
                <p class="imp-modal__sub">以下のディーラーに切り替えます</p>
                <p class="imp-modal__name">{{ $this->selectedDealerName }}</p>
                @if ($this->selectedDealerAddress)
                    <p class="imp-modal__addr">{{ $this->selectedDealerAddress }}</p>
                @endif
                <p class="imp-modal__notice">
                    設定中はベル通知・各管理画面のデータがこのディーラーの内容で表示されます
                </p>
                <div class="imp-modal__actions">
                    <button wire:click="$set('showModal', false)" class="imp-modal__cancel">キャンセル</button>
                    <button wire:click="confirmImpersonate" class="imp-modal__ok">OK</button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
