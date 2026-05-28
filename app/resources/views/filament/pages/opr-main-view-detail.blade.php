<x-filament-panels::page>
    @once
    @push('styles')
    <style>
        .preview-section { margin-bottom: 24px; }
        .preview-label { font-size: 14px; font-weight: 600; color: #ffffff; margin-bottom: 8px; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .preview-hero { position: relative; width: 100%; overflow: hidden; border-radius: 8px; background: #0a0a0a; }
        .preview-hero__img { width: 100%; height: auto; display: block; }
        .preview-hero__no-image { width: 100%; height: 300px; display: flex; align-items: center; justify-content: center; color: #666; font-size: 14px; background: #1a1a1a; }
        .preview-hero__overlay { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.5); }
        .preview-hero__inner { position: absolute; top: 40px; left: 80px; z-index: 1; }
        .preview-hero__label { font-size: 11px; font-weight: 500; color: #dc5078; letter-spacing: 0.2em; margin: 0 0 12px; }
        .preview-hero__title { font-size: 48px; font-weight: 300; color: #fff; letter-spacing: 0.05em; margin: 0 0 12px; line-height: 1.2; }
        .preview-hero__sub { font-size: 13px; font-weight: 300; color: #aaa; letter-spacing: 0.05em; margin: 0; }
    </style>
    @endpush
    @endonce
    
    {{-- ステータス表示 --}}
    @if($this->record->is_active)
        <div class="p-4 bg-green-900 border border-green-700 rounded-lg text-sm text-green-300">
            ✅ 現在 <strong>有効</strong> で表示されています
        </div>
    @else
        <div class="p-4 bg-red-900 border border-red-700 rounded-lg text-sm text-red-300">
            ❌ 現在 <strong>無効</strong> で非表示になっています
        </div>
    @endif

    <div class="preview-section">
        <div class="preview-label">TOPページプレビュー</div>
        <div class="preview-hero">
            @if($this->record->image_path)
                <img src="{{ Storage::disk('s3')->url($this->record->image_path) }}" class="preview-hero__img" alt="" />
            @else
                <div class="preview-hero__no-image">画像未設定</div>
            @endif
            <div class="preview-hero__overlay"></div>
            <div class="preview-hero__inner">
                <p class="preview-hero__label">{{ $this->record->label ?? 'PREMIUM CAR SEARCH' }}</p>
                <h2 class="preview-hero__title">{{ $this->record->title ?? 'タイトル未設定' }}</h2>
                <p class="preview-hero__sub">{{ $this->record->sub ?? 'サブテキスト未設定' }}</p>
            </div>
        </div>
    </div>

    {{-- 画像アップロード注意書き --}}
    <div class="p-4 bg-blue-900 border border-blue-700 rounded-lg text-sm text-blue-300">
        ⚠️ 推奨サイズ: <strong>横1200px × 縦600px</strong><br>
        ※ 異なるサイズの画像を登録した場合、TOPページでスライド毎に高さが変わる場合があります。
    </div>
    
    @livewire('asset-image-manager', ['type' => 'opr_main_view', 'recordId' => $this->record->id])

    <div class="p-4 bg-gray-800 border border-gray-600 rounded-lg text-sm text-gray-300">
        📌 表示ルール<br>
        ・<strong>有効</strong> かつ <strong>期間内</strong> の場合のみTOPページに表示されます。<br>
        ・無効の場合は期間設定に関わらず表示されません。<br>
        ・期間を設定しない場合は有効な間は常に表示されます。
    </div>
    
    {{ $this->infoList }}
</x-filament-panels::page>