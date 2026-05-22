<x-filament-panels::page>

    {{-- Alpine.dataに登録 --}}
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
                console.log('選択ファイル数（有効）', files.length);
                this.selectedFiles = files;
                this.fileCount     = files.length;
                this.total         = files.length;
            },

            async uploadAll(wire) {
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

                        await wire.uploadImageChunk(formData);
                        this.current  += chunk.length;
                        this.progress  = Math.round((this.current / this.total) * 100);
                    }

                    this.uploading = false;
                    await wire.validateImages();

                } catch (e) {
                    console.error('uploadAllエラー', e);
                    this.uploading = false;
                }
            }
        }));
    });
    </script>

    {{-- ===== タブ切り替え ===== --}}
    <div style="display: flex; gap: 0; margin-bottom: 24px; border-bottom: 2px solid #e5e7eb;">
        <button
            type="button"
            wire:click="switchTab('upload')"
            style="padding: 10px 24px; font-size: 13px; font-weight: 500; border: none; cursor: pointer; border-radius: 8px 8px 0 0; margin-bottom: -2px;
                background: {{ $activeTab === 'upload' ? '#185FA5' : 'transparent' }};
                color: {{ $activeTab === 'upload' ? 'white' : '#6b7280' }};
                border-bottom: 2px solid {{ $activeTab === 'upload' ? '#185FA5' : 'transparent' }};"
        >
            アップロード
        </button>
        <button
            type="button"
            wire:click="switchTab('history')"
            style="padding: 10px 24px; font-size: 13px; font-weight: 500; border: none; cursor: pointer; border-radius: 8px 8px 0 0; margin-bottom: -2px;
                background: {{ $activeTab === 'history' ? '#185FA5' : 'transparent' }};
                color: {{ $activeTab === 'history' ? 'white' : '#6b7280' }};
                border-bottom: 2px solid {{ $activeTab === 'history' ? '#185FA5' : 'transparent' }};"
        >
            履歴
            @if(collect($batches)->where('status', 'rejected')->count() > 0)
                <span style="display: inline-block; width: 8px; height: 8px; background: #E24B4A; border-radius: 50%; margin-left: 4px; vertical-align: middle;"></span>
            @endif
        </button>
    </div>

    {{-- ===== アップロードタブ ===== --}}
    @if($activeTab === 'upload')

    {{-- STEP1・STEP2・STEP3はコンポーネントで共通化 --}}
    @if(in_array($step, ['STEP1', 'STEP2', 'STEP3']))
        <x-bulk-upload-steps
            :upload-step="$step"
            :preview-summary="$previewSummary"
            :xlsx-errors="$xlsxErrors"
            :image-errors="$imageErrors"
            :validated-rows="$validatedRows"
            :uploaded-image-map="$uploadedImageMap"
            :imported-car-ids="$importedCarIds"
            title="アップロード"
            alpine-data-name="imageUploader"
        />
    @endif

    {{-- DONE: 完了（アップロードページ独自のUI） --}}
    @if($step === 'DONE')
    <div style="max-width: 480px; margin: 40px auto; text-align: center;">
        <p style="font-size: 48px; margin: 0 0 16px;">✅</p>
        <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 8px;">承認依頼を送信しました</h2>
        <p style="font-size: 13px; color: #6b7280; margin: 0 0 24px;">
            {{ count($importedCarIds) }}台の車両を登録し、管理者に承認依頼を送りました。<br>
            承認されるまでしばらくお待ちください。
        </p>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button
                type="button"
                wire:click="switchTab('history')"
                style="padding: 12px 24px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
            >
                履歴を確認する
            </button>
            <a
                href="{{ \App\Filament\Resources\CarRegistrationResource::getUrl('index') }}"
                style="display: inline-block; padding: 12px 24px; background: #f3f4f6; color: #374151; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;"
            >
                車両一覧に戻る
            </a>
        </div>
    </div>
    @endif

    @endif {{-- end activeTab === 'upload' --}}

    {{-- ===== 履歴タブ ===== --}}
    @if($activeTab === 'history')
    <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; overflow: hidden;">
        @if(empty($batches))
            <p style="text-align: center; color: #9ca3af; padding: 40px; font-size: 13px;">登録履歴がありません</p>
        @else
        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead style="background: #f9fafb;">
                <tr>
                    <th style="padding: 10px 14px; text-align: left; color: #374151; border-bottom: 1px solid #e5e7eb;">バッチID</th>
                    <th style="padding: 10px 14px; text-align: left; color: #374151; border-bottom: 1px solid #e5e7eb;">アップロード日時</th>
                    <th style="padding: 10px 14px; text-align: center; color: #374151; border-bottom: 1px solid #e5e7eb;">合計</th>
                    <th style="padding: 10px 14px; text-align: center; color: #374151; border-bottom: 1px solid #e5e7eb;">承認待ち</th>
                    <th style="padding: 10px 14px; text-align: center; color: #374151; border-bottom: 1px solid #e5e7eb;">承認済み</th>
                    <th style="padding: 10px 14px; text-align: center; color: #374151; border-bottom: 1px solid #e5e7eb;">差し戻し</th>
                    <th style="padding: 10px 14px; text-align: center; color: #374151; border-bottom: 1px solid #e5e7eb;">ステータス</th>
                    <th style="padding: 10px 14px; text-align: center; color: #374151; border-bottom: 1px solid #e5e7eb;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($batches as $batch)
                @php
                    $statusColor = match($batch['status']) {
                        'published'        => '#15803d',
                        'approved_pending' => '#1d4ed8',
                        'rejected'         => '#dc2626',
                        'partial_rejected' => '#92400e',
                        'pending'          => '#92400e',
                        default            => '#6b7280',
                    };
                    $statusBg = match($batch['status']) {
                        'published'        => '#f0fdf4',
                        'approved_pending' => '#eff6ff',
                        'rejected'         => '#fef2f2',
                        'partial_rejected' => '#fef3c7',
                        'pending'          => '#fef3c7',
                        default            => '#f3f4f6',
                    };
                @endphp
                <tr style="border-bottom: 1px solid #f3f4f6; {{ $batch['status'] === 'rejected' || $batch['status'] === 'partial_rejected' ? 'background: #fff9f9;' : '' }}">
                    <td style="padding: 10px 14px; color: #374151; font-weight: 500;">#{{ $batch['id'] }}</td>
                    <td style="padding: 10px 14px; color: #6b7280;">{{ $batch['uploaded_at'] }}</td>
                    <td style="padding: 10px 14px; text-align: center; color: #374151;">{{ $batch['total_count'] }}台</td>
                    <td style="padding: 10px 14px; text-align: center; color: {{ $batch['pending_count'] > 0 ? '#92400e' : '#9ca3af' }};">{{ $batch['pending_count'] }}台</td>
                    <td style="padding: 10px 14px; text-align: center; color: {{ $batch['approved_count'] > 0 ? '#15803d' : '#9ca3af' }};">{{ $batch['approved_count'] }}台</td>
                    <td style="padding: 10px 14px; text-align: center; color: {{ $batch['rejected_count'] > 0 ? '#dc2626' : '#9ca3af' }};">{{ $batch['rejected_count'] }}台</td>
                    <td style="padding: 10px 14px; text-align: center;">
                        <span style="font-size: 11px; font-weight: 500; color: {{ $statusColor }}; background: {{ $statusBg }}; padding: 2px 8px; border-radius: 4px;">
                            {{ $batch['status_label'] }}
                        </span>
                    </td>
                    <td style="padding: 10px 14px; text-align: center;">
                        <a
                            href="{{ $batch['detail_url'] }}"
                            style="font-size: 11px; color: #185FA5; text-decoration: none; border: 0.5px solid #185FA5; padding: 4px 10px; border-radius: 6px;"
                        >
                            詳細
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

</x-filament-panels::page>