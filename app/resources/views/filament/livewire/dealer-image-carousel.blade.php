<div>
    @if(count($images) > 0)

    {{-- ===== メイン画像スライダー ===== --}}
    <div style="position: relative; border-radius: 12px; overflow: hidden; background: #111827; margin-bottom: 12px;">

        {{-- メイン画像 --}}
        <div style="height: 300px; overflow: hidden;">
            <img
                src="{{ $images[$currentIndex]['url'] }}"
                alt="{{ $images[$currentIndex]['alt'] }}"
                style="width: 100%; height: 100%; object-fit: cover; transition: opacity 0.2s ease;"
            />
        </div>

        {{-- メインバッジ --}}
        @if($images[$currentIndex]['is_main'])
            <span style="position: absolute; top: 12px; right: 12px; background: #FAC775; color: #633806; font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 500;">メイン</span>
        @endif

        {{-- キャプション --}}
        @if($images[$currentIndex]['caption'])
            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.7)); padding: 24px 16px 12px;">
                <p style="font-size: 13px; color: white; margin: 0;">{{ $images[$currentIndex]['caption'] }}</p>
            </div>
        @endif

        {{-- 前へボタン --}}
        @if(count($images) > 1)
            <button
                type="button"
                wire:click="prev"
                style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
            >‹</button>

            {{-- 次へボタン --}}
            <button
                type="button"
                wire:click="next"
                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
            >›</button>
        @endif

        {{-- 枚数インジケーター --}}
        <div style="position: absolute; bottom: 12px; right: 12px; background: rgba(0,0,0,0.5); color: white; font-size: 11px; padding: 2px 8px; border-radius: 10px;">
            {{ $currentIndex + 1 }} / {{ count($images) }}
        </div>
    </div>

    {{-- ===== サムネイル一覧 ===== --}}
    @if(count($images) > 1)
        <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;">
            @foreach($images as $index => $image)
                <div
                    wire:click="setIndex({{ $index }})"
                    style="flex-shrink: 0; width: 72px; height: 54px; border-radius: 6px; overflow: hidden; cursor: pointer; border: {{ $currentIndex === $index ? '2px solid #185FA5' : '2px solid transparent' }}; opacity: {{ $currentIndex === $index ? '1' : '0.6' }}; transition: opacity 0.2s;"
                >
                    <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" style="width: 100%; height: 100%; object-fit: cover;" />
                </div>
            @endforeach
        </div>
    @endif

    @else
        <div style="height: 200px; background: #f3f4f6; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <p style="font-size: 13px; color: #9ca3af;">画像が登録されていません</p>
        </div>
    @endif
</div>