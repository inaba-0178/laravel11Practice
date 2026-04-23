<x-filament-panels::page>
    <x-filament::section>
        {{-- 検索フォーム --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 16px;">
            <div>
                <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">エリア</label>
                <select
                    wire:model.live="selectedAreaId"
                    style="width: 100%; height: 36px; padding: 0 10px; font-size: 13px; border: 0.5px solid #374151; border-radius: 8px; background: #1f2937; color: #f9fafb;"
                >
                    <option value="">すべて</option>
                    @foreach($this->areas as $area)
                        <option value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">都道府県</label>
                <select
                    wire:model.live="selectedRegionId"
                    style="width: 100%; height: 36px; padding: 0 10px; font-size: 13px; border: 0.5px solid #374151; border-radius: 8px; background: #1f2937; color: #f9fafb;"
                >
                    <option value="">すべて</option>
                    @foreach($this->regions as $region)
                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">ディーラー名</label>
                <input
                    type="text"
                    wire:model.live.debounce.500ms="searchName"
                    placeholder="店舗名を入力（3文字以上）"
                    style="width: 100%; height: 36px; padding: 0 10px; font-size: 13px; border: 0.5px solid #374151; border-radius: 8px; background: #1f2937; color: #f9fafb;"
                />
            </div>
        </div>

        {{-- 絞り込み前メッセージ --}}
        @if(!$this->hasSearchCondition())
            <p style="font-size: 13px; color: #6b7280; text-align: center; padding: 32px 0;">
                エリア・都道府県・ディーラー名で絞り込んでください
            </p>
        @else
            {{-- 検索結果テーブル --}}
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 12px;">
                <thead>
                    <tr style="background: #1f2937; border-bottom: 1px solid #374151;">
                        <th style="width: 40px; padding: 10px 16px;"></th>
                        <th style="text-align: left; padding: 10px 16px; color: #9ca3af; font-weight: 500;">店舗名</th>
                        <th style="text-align: left; padding: 10px 16px; color: #9ca3af; font-weight: 500;">住所</th>
                        <th style="text-align: left; padding: 10px 16px; color: #9ca3af; font-weight: 500;">電話番号</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->dealers as $dealer)
                        @php
                            $isSelected = isset($selectedStores[$dealer->id]);
                            $address    = ($dealer->city ?? '') . ($dealer->address_detail ?? '');
                        @endphp
                        <tr
                            wire:click="toggleStore({{ $dealer->id }}, '{{ addslashes($dealer->name) }}', '{{ addslashes($address) }}', '{{ $dealer->phone ?? '' }}')"
                            style="border-bottom: 0.5px solid #374151; cursor: pointer; background: {{ $isSelected ? '#1e3a5f' : 'transparent' }};"
                        >
                            <td style="padding: 10px 16px;">
                                <input
                                    type="checkbox"
                                    {{ $isSelected ? 'checked' : '' }}
                                    wire:click.stop="toggleStore({{ $dealer->id }}, '{{ addslashes($dealer->name) }}', '{{ addslashes($address) }}', '{{ $dealer->phone ?? '' }}')"
                                    style="width: 16px; height: 16px; cursor: pointer;"
                                />
                            </td>
                            <td style="padding: 10px 16px; color: {{ $isSelected ? '#93c5fd' : '#e5e7eb' }};">{{ $dealer->name }}</td>
                            <td style="padding: 10px 16px; color: {{ $isSelected ? '#93c5fd' : '#9ca3af' }};">{{ $address }}</td>
                            <td style="padding: 10px 16px; color: {{ $isSelected ? '#93c5fd' : '#9ca3af' }};">{{ $dealer->phone ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 32px 16px; text-align: center; color: #6b7280;">
                                店舗が見つかりませんでした
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- ページネーション --}}
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                <p style="font-size: 13px; color: #6b7280; margin: 0;">
                    {{ $this->dealers->perPage() }}件ずつ表示
                </p>
                <div style="display: flex; gap: 8px;">
                    @if($this->dealers->onFirstPage())
                        <span style="font-size: 13px; color: #4b5563; padding: 6px 14px; border: 0.5px solid #374151; border-radius: 6px;">前へ</span>
                    @else
                        <button
                            wire:click="previousPage"
                            style="font-size: 13px; color: #e5e7eb; padding: 6px 14px; border: 0.5px solid #374151; border-radius: 6px; background: transparent; cursor: pointer;"
                        >前へ</button>
                    @endif

                    @if($this->dealers->hasMorePages())
                        <button
                            wire:click="nextPage"
                            style="font-size: 13px; color: #e5e7eb; padding: 6px 14px; border: 0.5px solid #374151; border-radius: 6px; background: transparent; cursor: pointer;"
                        >次へ</button>
                    @else
                        <span style="font-size: 13px; color: #4b5563; padding: 6px 14px; border: 0.5px solid #374151; border-radius: 6px;">次へ</span>
                    @endif
                </div>
            </div>
        @endif
    </x-filament::section>

    {{-- 選択中の店舗 --}}
    @if(count($selectedStores) > 0)
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <span>選択中の店舗</span>
                    <span style="font-size: 12px; color: #60a5fa;">{{ count($selectedStores) }}件選択中</span>
                </div>
            </x-slot>

            <div>
                @foreach($selectedStores as $dealerId => $store)
                    <div style="display: flex; align-items: center; padding: 12px 0; border-bottom: 0.5px solid #374151; gap: 16px;">
                        <div style="flex: 1;">
                            <p style="font-size: 13px; font-weight: 500; color: #e5e7eb; margin: 0 0 2px;">{{ $store['name'] }}</p>
                            <p style="font-size: 12px; color: #9ca3af; margin: 0;">{{ $store['address'] }}</p>
                        </div>
                        <select
                            wire:change="updateType({{ $dealerId }}, $event.target.value)"
                            style="height: 30px; font-size: 12px; border: 0.5px solid #374151; border-radius: 6px; background: #1f2937; color: #e5e7eb; padding: 0 6px; min-width: 90px; flex-shrink: 0;"
                        >
                            <option value="affiliated" {{ $store['type'] === 'affiliated' ? 'selected' : '' }}>系列店</option>
                            <option value="partner" {{ $store['type'] === 'partner' ? 'selected' : '' }}>提携店</option>
                        </select>
                        <button
                            wire:click="removeStore({{ $dealerId }})"
                            style="height: 30px; font-size: 12px; padding: 0 14px; border: 0.5px solid #374151; border-radius: 6px; background: transparent; color: #9ca3af; cursor: pointer; flex-shrink: 0;"
                        >解除</button>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
            <x-filament::button wire:click="confirmApply" color="primary">
                まとめて申請する（{{ count($selectedStores) }}件）
            </x-filament::button>
        </div>
    @endif

    {{-- 確認モーダル --}}
    @if($showConfirmModal)
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
            <div style="background: #1f2937; border-radius: 12px; padding: 24px; max-width: 400px; width: 100%; margin: 0 16px;">
                <p style="font-size: 15px; font-weight: 500; margin: 0 0 8px; color: #f9fafb;">申請しますか？</p>
                <p style="font-size: 13px; color: #9ca3af; margin: 0 0 16px;">
                    {{ count($selectedStores) }}件の店舗に申請します。<br>
                    申請先の店舗が承認するまで「申請中」になります。
                </p>
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button
                        wire:click="cancelConfirm"
                        style="padding: 8px 16px; font-size: 13px; border: 0.5px solid #374151; background: #374151; color: #e5e7eb; border-radius: 8px; cursor: pointer;"
                    >キャンセル</button>
                    <button
                        wire:click="apply"
                        style="padding: 8px 16px; font-size: 13px; border: none; background: #3b82f6; color: white; border-radius: 8px; cursor: pointer;"
                    >申請する</button>
                </div>
            </div>
        </div>
    @endif

</x-filament-panels::page>