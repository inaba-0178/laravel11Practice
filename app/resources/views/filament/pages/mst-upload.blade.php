<x-filament-panels::page>
    <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px;">

        {{-- ファイルアップロードエリア --}}
        <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 24px;">
            <p style="font-size: 14px; font-weight: 500; color: #374151; margin: 0 0 16px;">スプレッドシートをアップロード</p>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                {{-- ファイル選択 --}}
                <div>
                    <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">ファイル選択（.xlsx）</label>
                    <input
                        type="file"
                        accept=".xlsx"
                        x-ref="fileInput"
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
                    <label style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">バージョンタイプ</label>
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
                        >
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                    <p style="font-size: 13px; color: #185FA5; margin: 8px 0 0; font-weight: 500;">次のバージョン：{{ $version }}</p>
                </div>

                {{-- アップロードボタン --}}
                <button
                    type="button"
                    wire:click="uploadAndValidate"
                   
                    style="padding: 10px 20px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
                >
                    <span wire:loading.remove wire:target="uploadAndValidate">アップロード</span>
                    <span wire:loading wire:target="uploadAndValidate">処理中...</span>
                </button>

                {{-- ローディングオーバーレイ --}}
                {{-- <div wire:loading wire:target="uploadAndValidate" style="position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 9999;">
                    <div style="background: white; border-radius: 12px; padding: 32px 48px; text-align: center;">
                        <p style="font-size: 16px; font-weight: 500; color: #374151; margin: 0 0 8px;">バリデーション中...</p>
                        <p style="font-size: 13px; color: #6b7280; margin: 0;">しばらくお待ちください</p>
                    </div>
                </div> --}}

            </div>
        </div>

        {{-- 完了メッセージ --}}
        @if($importCompleted)
        <div style="background: #f0fdf4; border-radius: 12px; border: 0.5px solid #bbf7d0; padding: 20px; text-align: center;">
            <p style="font-size: 15px; font-weight: 500; color: #15803d; margin: 0;">✅ データ投入が完了しました</p>
            <p style="font-size: 12px; color: #6b7280; margin: 8px 0 0;">バージョン管理ページで承認申請を行ってください</p>
        </div>
        @endif
    </div>

    {{-- モーダル --}}
    @if($showModal)
    <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
        <div style="background: white; border-radius: 12px; width: 90%; max-width: 700px; max-height: 85vh; overflow-y: auto; padding: 24px;">

            {{-- バリデーション中 --}}
            @if($isValidating)
            <div style="text-align: center; padding: 40px;">
                <p style="font-size: 14px; color: #6b7280;">バリデーション中...</p>
            </div>

            {{-- エラーあり --}}
            @elseif(!empty($errors))
            <div>
                <p style="font-size: 15px; font-weight: 500; color: #dc2626; margin: 0 0 16px;">❌ バリデーションエラー（{{ count($errors) }}件）</p>
                <div style="display: flex; flex-direction: column; gap: 6px; max-height: 400px; overflow-y: auto;">
                    @foreach($errors as $error)
                    <div style="background: #fef2f2; border: 0.5px solid #fca5a5; border-radius: 8px; padding: 10px 14px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 11px; background: #dc2626; color: white; padding: 1px 8px; border-radius: 4px; white-space: nowrap;">
                                {{ $error['sheet'] }}
                                @if($error['line']) {{ $error['line'] }}行目 @endif
                            </span>
                            <span style="font-size: 12px; color: #991b1b;">{{ $error['message'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div style="margin-top: 16px; display: flex; justify-content: flex-end;">
                    <button type="button" wire:click="closeModal" style="padding: 8px 16px; background: #f3f4f6; border: 0.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; cursor: pointer;">閉じる</button>
                </div>
            </div>

            {{-- エラーなし・プレビュー --}}
            @else
            <div>
                <p style="font-size: 15px; font-weight: 500; color: #374151; margin: 0 0 16px;">✅ バリデーション通過　内容を確認してください</p>

                {{-- プレビュー件数 --}}
                <div style="border: 0.5px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 16px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; background: #f9fafb; padding: 8px 14px; border-bottom: 0.5px solid #e5e7eb;">
                        <span style="font-size: 11px; font-weight: 500; color: #374151;">シート名</span>
                        <span style="font-size: 11px; font-weight: 500; color: #374151; text-align: center;">現在の件数</span>
                        <span style="font-size: 11px; font-weight: 500; color: #374151; text-align: center;">新しい件数</span>
                    </div>
                    @foreach($previewCounts as $sheetName => $count)
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; padding: 8px 14px; border-bottom: 0.5px solid #f3f4f6;">
                        <span style="font-size: 12px; color: #374151;">{{ $sheetName }}</span>
                        <span style="font-size: 12px; color: #6b7280; text-align: center;">{{ number_format($count['current_count']) }}件</span>
                        <span style="font-size: 12px; color: #185FA5; text-align: center; font-weight: 500;">{{ number_format($count['new_count']) }}件</span>
                    </div>
                    @endforeach
                </div>

                {{-- バージョン・説明入力 --}}
                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;">
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
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 12px; color: #374151;">{{ $currentTable }} を処理中...</span>
                        <span style="font-size: 12px; color: #185FA5; font-weight: 500;">{{ $importProgress }} / {{ $importTotal }}</span>
                    </div>
                    <div style="background: #e5e7eb; border-radius: 6px; height: 10px; overflow: hidden;">
                        <div style="width: {{ $importTotal > 0 ? round(($importProgress / $importTotal) * 100) : 0 }}%; background: #185FA5; height: 100%; border-radius: 6px; transition: width 0.3s ease;"></div>
                    </div>
                </div>
                @endif

                {{-- ボタン --}}
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" wire:click="closeModal" style="padding: 8px 16px; background: #f3f4f6; border: 0.5px solid #e5e7eb; border-radius: 8px; font-size: 13px; cursor: pointer;">キャンセル</button>
                    <button
                        type="button"
                        wire:click="import"
                        wire:loading.attr="disabled"
                        style="padding: 8px 20px; background: #3B6D11; color: #EAF3DE; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
                    >
                        <span wire:loading.remove wire:target="import">データ投入実行</span>
                        <span wire:loading wire:target="import">投入中...</span>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</x-filament-panels::page>