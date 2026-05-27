<x-filament-panels::page>

{{-- Alpine.dataはdivより先に定義する（Filamentでalpine:initが既に発火済みの場合の対策） --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('imageUploader', () => ({
        uploading:     false,
        fileCount:     0,
        total:         0,
        current:       0,
        progress:      0,
        selectedFiles: [],

        handleFiles(event) {
            const files = Array.from(event.target.files).filter(f => f.size > 0);
            this.selectedFiles = files;
            this.fileCount     = files.length;
            this.total         = files.length;
        },

        async uploadAll() {
            if (this.selectedFiles.length === 0) {
                alert('画像ファイルを選択してください。');
                return;
            }
            this.uploading = true;
            this.current   = 0;
            this.progress  = 0;

            const chunkSize = 10;

            try {
                for (let i = 0; i < this.selectedFiles.length; i += chunkSize) {
                    const chunk    = this.selectedFiles.slice(i, i + chunkSize);
                    const formData = [];

                    for (const file of chunk) {
                        const base64 = await new Promise((resolve, reject) => {
                            const r   = new FileReader();
                            r.onload  = e => resolve(e.target.result.split(',')[1]);
                            r.onerror = e => reject(e);
                            r.readAsDataURL(file);
                        });
                        formData.push({
                            name:         file.name,
                            base64:       base64,
                            relativePath: file.webkitRelativePath,
                        });
                    }

                    // $wire を引数で渡さず magic property として直接使う
                    await this.$wire.uploadImageChunk(formData);
                    this.current  += chunk.length;
                    this.progress  = Math.round((this.current / this.total) * 100);
                }

                this.uploading = false;
                await this.$wire.validateImages();

            } catch (e) {
                console.error('uploadAll error:', e);
                this.uploading = false;
            }
        }
    }));
});
</script>

<div x-data="imageUploader()" style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    {{-- ===== STEPインジケーター ===== --}}
    @php
        $steps = [
            'STEP1' => 'xlsxアップロード',
            'STEP2' => '画像アップロード',
            'STEP3' => '確認・投入',
            'DONE'  => '完了',
        ];
        $stepKeys  = array_keys($steps);
        $currentIdx = array_search($step, $stepKeys);
    @endphp
    <div style="display: flex; align-items: center; gap: 0;">
        @foreach($steps as $key => $label)
        @php
            $idx      = array_search($key, $stepKeys);
            $isActive = $step === $key;
            $isDone   = $currentIdx > $idx;
        @endphp
        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative;">
            @if(!$loop->first)
            <div style="position: absolute; left: -50%; top: 16px; width: 100%; height: 2px;
                background: {{ $isDone ? '#185FA5' : '#e5e7eb' }};"></div>
            @endif
            <div style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; z-index: 1;
                background: {{ $isActive ? '#185FA5' : ($isDone ? '#185FA5' : '#e5e7eb') }};
                color: {{ $isActive || $isDone ? 'white' : '#9ca3af' }};">
                @if($isDone)✓@else{{ $idx + 1 }}@endif
            </div>
            <span style="font-size: 11px; margin-top: 6px; font-weight: {{ $isActive ? '600' : '400' }};
                color: {{ $isActive ? '#185FA5' : ($isDone ? '#374151' : '#9ca3af') }};">
                {{ $label }}
            </span>
        </div>
        @endforeach
    </div>

    {{-- ===== STEP1: xlsxアップロード ===== --}}
    @if($step === 'STEP1')
    <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 28px;">
        <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 20px;">スプレッドシートをアップロード</p>

        <div style="display: flex; flex-direction: column; gap: 16px;">

            {{-- ファイル選択 --}}
            <div>
                <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 6px;">ファイル選択（.xlsx）</label>
                <input
                    type="file"
                    accept=".xlsx"
                    x-on:change="
                        const file = $event.target.files[0];
                        if (!file) return;
                        const formData = new FormData();
                        formData.append('file', file);
                        fetch('/admin/mst-upload/upload', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                            body: formData
                        })
                        .then(r => r.json())
                        .then(data => $wire.set('uploadedFilePath', data.path))
                    "
                    style="display: block; width: 100%; font-size: 13px; color: #6b7280; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 12px; background: #f9fafb; box-sizing: border-box;"
                />
            </div>

            {{-- バージョンタイプ選択 --}}
            <div>
                <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 6px;">バージョンタイプ</label>
                <div style="display: flex; gap: 8px;">
                    @foreach(['patch' => 'パッチ（0.0.x）', 'minor' => 'マイナー（0.x.0）', 'major' => 'メジャー（x.0.0）'] as $type => $label)
                    <button
                        type="button"
                        wire:click="updateVersionType('{{ $type }}')"
                        style="padding: 6px 14px; border-radius: 8px; font-size: 12px; cursor: pointer;
                            {{ $versionType === $type
                                ? 'background: #185FA5; color: white; border: none;'
                                : 'background: #f3f4f6; color: #374151; border: 0.5px solid #e5e7eb;'
                            }}"
                    >{{ $label }}</button>
                    @endforeach
                </div>
                <p style="font-size: 13px; color: #185FA5; margin: 8px 0 0; font-weight: 500;">次のバージョン：{{ $version }}</p>
            </div>

            {{-- アップロード・バリデーションボタン --}}
            <div>
                <button
                    type="button"
                    wire:click="uploadAndValidate"
                    wire:loading.attr="disabled"
                    style="padding: 10px 24px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
                >
                    <span wire:loading.remove wire:target="uploadAndValidate">バリデーション実行</span>
                    <span wire:loading wire:target="uploadAndValidate">処理中...</span>
                </button>
            </div>

            {{-- バリデーションエラー --}}
            @if(!empty($xlsxErrors))
            <div>
                <p style="font-size: 13px; font-weight: 600; color: #dc2626; margin: 0 0 10px;">❌ バリデーションエラー（{{ count($xlsxErrors) }}件）</p>
                <div style="display: flex; flex-direction: column; gap: 6px; max-height: 320px; overflow-y: auto;">
                    @foreach($xlsxErrors as $error)
                    <div style="background: #fef2f2; border: 0.5px solid #fca5a5; border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 11px; background: #dc2626; color: white; padding: 1px 8px; border-radius: 4px; white-space: nowrap;">
                            {{ $error['sheet'] }}{{ $error['line'] ? ' ' . $error['line'] . '行目' : '' }}
                        </span>
                        <span style="font-size: 12px; color: #991b1b;">{{ $error['message'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
    @endif

    {{-- ===== STEP2: 画像アップロード ===== --}}
    @if($step === 'STEP2')
    <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 28px;">
        <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 6px;">画像フォルダをアップロード</p>
        <p style="font-size: 12px; color: #6b7280; margin: 0 0 20px;">
            フォルダ構成：<code style="background:#f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 11px;">mst_manufacturer_images/toyota.jpg</code> のようにテーブル名フォルダ以下に画像を格納してください
        </p>

        {{-- 対象テーブル一覧 --}}
        @php
            $imageSheets = [];
            foreach(array_keys($sheets) as $sheetName) {
                $tbl = \App\Constants\MstTableMap::getTableName($sheetName);
                if ($tbl && \App\Constants\MstTableMap::hasImage($tbl)) {
                    $imageSheets[$tbl] = $sheetName;
                }
            }
        @endphp
        @if(!empty($imageSheets))
        <div style="background: #f0f7ff; border: 0.5px solid #bfdbfe; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;">
            <p style="font-size: 12px; font-weight: 500; color: #1d4ed8; margin: 0 0 8px;">画像が必要なテーブル</p>
            @foreach($imageSheets as $tbl => $sheetName)
            <p style="font-size: 12px; color: #374151; margin: 4px 0;">
                📁 <code style="background: white; padding: 1px 6px; border-radius: 4px; font-size: 11px;">{{ $tbl }}/</code>
                <span style="color: #6b7280; margin-left: 6px;">（シート：{{ $sheetName }}）</span>
            </p>
            @endforeach
        </div>
        @endif

        {{-- フォルダ選択 --}}
        <div style="margin-bottom: 16px;">
            <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 6px;">画像フォルダ選択</label>
            <input
                type="file"
                webkitdirectory
                multiple
                x-ref="imageInput"
                x-on:change="console.log('change fired', $event.target.files.length); handleFiles($event)"
                style="display: block; width: 100%; font-size: 13px; color: #6b7280; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 12px; background: #f9fafb; box-sizing: border-box;"
            />
            <p style="font-size: 12px; color: #374151; margin: 6px 0 0;">
                fileCount: <span x-text="fileCount"></span>
            </p>
        </div>

        {{-- アップロードボタン --}}
        {{-- アップロードボタン --}}
        <div style="margin-bottom: 16px;">
            <button
                type="button"
                x-on:click="uploadAll()"
                style="padding: 10px 24px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; display: block;"
            >
                画像をアップロードしてチェック
            </button>
        </div>

        {{-- プログレスバー --}}
        <div x-show="uploading" style="margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span style="font-size: 12px; color: #374151;">アップロード中...</span>
                <span style="font-size: 12px; color: #185FA5; font-weight: 500;">
                    <span x-text="current"></span> / <span x-text="total"></span>
                </span>
            </div>
            <div style="background: #e5e7eb; border-radius: 6px; height: 8px; overflow: hidden;">
                <div x-bind:style="'width: ' + progress + '%; background: #185FA5; height: 100%; border-radius: 6px; transition: width 0.3s ease;'"></div>
            </div>
        </div>

        {{-- アップロード済みファイル数表示 --}}
        @if($imageUploadCount > 0 && empty($imageErrors))
        <div style="background: #f0fdf4; border: 0.5px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
            <p style="font-size: 13px; color: #15803d; margin: 0;">✅ {{ $imageUploadCount }}ファイルをアップロード済み・チェック通過</p>
        </div>
        @endif

        {{-- 画像バリデーションエラー --}}
        @if(!empty($imageErrors))
        <div style="margin-bottom: 16px;">
            <p style="font-size: 13px; font-weight: 600; color: #dc2626; margin: 0 0 10px;">❌ 画像チェックエラー（{{ count($imageErrors) }}件）</p>
            <div style="display: flex; flex-direction: column; gap: 6px; max-height: 280px; overflow-y: auto;">
                @foreach($imageErrors as $error)
                <div style="background: #fef2f2; border: 0.5px solid #fca5a5; border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; background: #dc2626; color: white; padding: 1px 8px; border-radius: 4px; white-space: nowrap;">
                        {{ $error['sheet'] }}{{ $error['line'] ? ' ' . $error['line'] . '行目' : '' }}
                    </span>
                    <span style="font-size: 12px; color: #991b1b;">{{ $error['message'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- 戻るボタン --}}
        <button
            type="button"
            wire:click="backToStep1"
            style="padding: 8px 16px; background: #f3f4f6; border: 0.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; color: #374151; cursor: pointer;"
        >← xlsxに戻る</button>
    </div>
    @endif

    {{-- ===== STEP3: 確認・データ投入 ===== --}}
    @if($step === 'STEP3')
    <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 28px;">
        <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 20px;">✅ バリデーション通過　内容を確認してください</p>

        {{-- プレビュー件数 --}}
        <div style="border: 0.5px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; background: #f9fafb; padding: 8px 14px; border-bottom: 0.5px solid #e5e7eb;">
                <span style="font-size: 11px; font-weight: 500; color: #374151;">シート名</span>
                <span style="font-size: 11px; font-weight: 500; color: #374151; text-align: center;">現在の件数</span>
                <span style="font-size: 11px; font-weight: 500; color: #374151; text-align: center;">新しい件数</span>
            </div>
            @foreach($previewCounts as $sheetName => $count)
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; padding: 8px 14px; border-bottom: 0.5px solid #f3f4f6;">
                <span style="font-size: 12px; color: #374151;">{{ $sheetName }}</span>
                <span style="font-size: 12px; color: #6b7280; text-align: center;">{{ number_format($count['current_count']) }}件</span>
                <span style="font-size: 12px; color: #185FA5; font-weight: 500; text-align: center;">{{ number_format($count['new_count']) }}件</span>
            </div>
            @endforeach
        </div>

        {{-- 画像アップロード数 --}}
        @if($imageUploadCount > 0)
        <div style="background: #f0f7ff; border: 0.5px solid #bfdbfe; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;">
            <p style="font-size: 12px; color: #1d4ed8; margin: 0;">🖼 画像ファイル {{ $imageUploadCount }}件をS3にアップロードします</p>
            @foreach($uploadedImageMap as $tableName => $files)
            <p style="font-size: 11px; color: #374151; margin: 4px 0 0;">
                {{ $tableName }}：{{ count($files) }}件
            </p>
            @endforeach
        </div>
        @endif

        {{-- バージョン・説明入力 --}}
        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
            <div>
                <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">バージョン番号</label>
                <input type="text" wire:model="version" style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 13px; color: #111827; background: #f9fafb;" />
            </div>
            <div>
                <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">更新内容の説明</label>
                <textarea wire:model="description" rows="3" style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 13px; color: #111827; background: #f9fafb; resize: vertical;" placeholder="例：メーカー・車種データを最新版に更新"></textarea>
            </div>
        </div>

        {{-- プログレスバー（投入中） --}}
        @if($isImporting)
        <div style="margin-bottom: 16px;">
            <p style="font-size: 12px; color: #374151; margin: 0 0 6px;">データ投入中...</p>
            <div style="background: #e5e7eb; border-radius: 6px; height: 8px; overflow: hidden;">
                <div style="width: 100%; background: linear-gradient(90deg, #185FA5, #3b82f6); height: 100%; border-radius: 6px; animation: pulse 1.5s infinite;"></div>
            </div>
            <style>@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }</style>
        </div>
        @endif

        {{-- ボタン --}}
        <div style="display: flex; gap: 8px;">
            @if(!$isImporting)
            <button
                type="button"
                wire:click="backToStep2"
                style="padding: 10px 16px; background: #f3f4f6; border: 0.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; color: #374151; cursor: pointer;"
            >← 画像に戻る</button>
            @endif
            <button
                type="button"
                wire:click="import"
                wire:loading.attr="disabled"
                style="padding: 10px 24px; background: #3B6D11; color: #EAF3DE; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
            >
                <span wire:loading.remove wire:target="import">データ投入実行</span>
                <span wire:loading wire:target="import">投入中...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- ===== DONE: 完了 ===== --}}
    @if($step === 'DONE')
    <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 40px; text-align: center;">
        <p style="font-size: 40px; margin: 0 0 16px;">✅</p>
        <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 8px;">データ投入が完了しました</h2>
        <p style="font-size: 13px; color: #6b7280; margin: 0 0 24px;">
            バージョン <strong>{{ $version }}</strong> のデータを登録しました。
            @if($imageUploadCount > 0)
            <br>画像 {{ $imageUploadCount }}件をS3にアップロードしました。
            @endif
        </p>
        <button
            type="button"
            wire:click="backToStep1"
            style="padding: 10px 24px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
        >続けてアップロード</button>
    </div>
    @endif

</div>

</x-filament-panels::page>