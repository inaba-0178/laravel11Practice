<x-filament-panels::page>
    @php
        $cars    = $record->cars->sortBy('id')->values();
        $car     = $this->getCurrentCar();
        $images  = $this->getImages();
        $reason  = $car?->rejection_reason ?? [];
        $generalComment  = $reason['general_comment'] ?? '';
        $flaggedImages   = $reason['flagged_images'] ?? [];
        $items           = $reason['items'] ?? [];
        $generalResponse = $generalResponses[$currentCarId] ?? '';
        $imgResponses    = $imageResponses[$currentCarId] ?? [];
        $itmResponses    = $itemResponses[$currentCarId] ?? [];
    @endphp

    {{-- Alpine.js for image uploader --}}
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imageUploaderDetail', () => ({
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

    {{-- ===== バッチ情報 ===== --}}
    <div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; padding: 14px 18px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">
                バッチ #{{ $record->id }} ／ {{ $record->uploaded_at?->format('Y/m/d H:i') }}
            </p>
            <p style="font-size: 11px; color: #6b7280; margin: 4px 0 0;">
                合計：{{ $record->total_count }}台　
                承認待ち：{{ $record->pending_count }}台　
                承認済み：{{ $record->approved_count }}台　
                差し戻し：{{ $record->rejected_count }}台
            </p>
        </div>
        <a
            href="{{ \App\Filament\Pages\BulkCarUploadPage::getUrl() }}"
            style="padding: 8px 16px; background: #6b7280; color: white; border-radius: 8px; font-size: 12px; font-weight: 500; text-decoration: none;"
        >
            一括登録ページへ
        </a>
    </div>

    {{-- ===== 操作説明パネル ===== --}}
    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 16px 20px; margin-bottom: 16px;">
        <p style="font-size: 13px; font-weight: 600; color: #0369a1; margin: 0 0 12px;">📋 差し戻し対応の手順</p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="background: white; border-radius: 8px; padding: 14px; border: 0.5px solid #bae6fd;">
                <p style="font-size: 12px; font-weight: 600; color: #0369a1; margin: 0 0 8px;">📄 車両情報を修正する場合</p>
                <ol style="font-size: 11px; color: #374151; margin: 0; padding-left: 16px; line-height: 1.8;">
                    <li>差し戻し内容を確認する</li>
                    <li>スプレッドシートまたはxlsxファイルの該当車両を修正する</li>
                    <li>操作列を「更新」に変更する</li>
                    <li>下記の再アップロードエリアからアップロードする</li>
                </ol>
            </div>
            <div style="background: white; border-radius: 8px; padding: 14px; border: 0.5px solid #bae6fd;">
                <p style="font-size: 12px; font-weight: 600; color: #0369a1; margin: 0 0 8px;">🖼️ 画像を差し替える場合</p>
                <ol style="font-size: 11px; color: #374151; margin: 0; padding-left: 16px; line-height: 1.8;">
                    <li>指摘された画像のファイル名を確認する（下記に表示）</li>
                    <li>同じファイル名で新しい画像を用意する</li>
                    <li>同じフォルダ構造で再アップロードする</li>
                    <li>スプレッドシートまたはxlsxファイルの操作列を「更新」にして再アップロードする</li>
                </ol>
            </div>
        </div>
        <div style="background: #fef3c7; border-radius: 8px; padding: 10px 14px; margin-top: 12px; border: 0.5px solid #fcd34d;">
            <p style="font-size: 11px; color: #92400e; margin: 0;">
                ⚠️ 注意：再アップロード時はスプレッドシートまたはxlsxファイルの操作列を必ず「更新」にしてください。「新規」にすると重複登録になります。
            </p>
        </div>
    </div>

    {{-- ===== タブ＋メインコンテンツ ===== --}}
    <div style="border: 2px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">

        {{-- 車両タブ --}}
        <div style="display: flex; overflow-x: auto; gap: 6px; padding: 12px 12px 0; border-bottom: 2px solid #e5e7eb;">
            @foreach($cars as $tabCar)
            @php
                $isActive    = $tabCar->id === $currentCarId;
                $isRejected  = $tabCar->status === \App\Constants\CarStatus::REJECTED;
                $hasResponse = !empty($generalResponses[$tabCar->id])
                    || !empty(array_filter($imageResponses[$tabCar->id] ?? []))
                    || !empty(array_filter($itemResponses[$tabCar->id] ?? []));

                $statusColor = match($tabCar->status) {
                    'available'        => '#15803d',
                    'approved_pending' => '#1d4ed8',
                    'rejected'         => '#dc2626',
                    'pending'          => '#92400e',
                    default            => '#6b7280',
                };
                $statusBg = match($tabCar->status) {
                    'available'        => '#f0fdf4',
                    'approved_pending' => '#eff6ff',
                    'rejected'         => '#fef2f2',
                    'pending'          => '#fef3c7',
                    default            => '#f3f4f6',
                };
                $thumb    = $tabCar->images->sortBy('display_order')->first();
                $thumbUrl = $thumb ? Storage::disk('s3')->url($thumb->image_url) : null;
            @endphp
            <div
                style="
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 8px 14px;
                    cursor: pointer;
                    border-radius: 8px 8px 0 0;
                    background: {{ $isActive ? '#fce7f3' : '#e0f2fe' }};
                    border: 2px solid {{ $isActive ? '#e879a0' : '#bae6fd' }};
                    border-bottom: 2px solid {{ $isActive ? '#fce7f3' : '#e0f2fe' }};
                    white-space: nowrap;
                    position: relative;
                    margin-bottom: -2px;
                "
                wire:click="selectCar({{ $tabCar->id }})"
            >
                <div style="width: 36px; height: 36px; border-radius: 6px; overflow: hidden; flex-shrink: 0; background: #f3f4f6;">
                    @if($thumbUrl)
                        <img src="{{ $thumbUrl }}" style="width: 100%; height: 100%; object-fit: cover;" />
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 16px;">🚗</div>
                    @endif
                </div>
                <div>
                    <p style="font-size: 12px; font-weight: {{ $isActive ? '600' : '400' }}; color: {{ $isActive ? '#be185d' : '#374151' }}; margin: 0 0 2px;">
                        {{ $tabCar->series?->series_name ?? '不明' }}
                        @if($isRejected && !$hasResponse)
                            <span style="display: inline-block; width: 6px; height: 6px; background: #E24B4A; border-radius: 50%; margin-left: 3px; vertical-align: middle;"></span>
                        @endif
                    </p>
                    <span style="font-size: 10px; font-weight: 500; color: {{ $statusColor }}; background: {{ $statusBg }}; padding: 1px 6px; border-radius: 4px;">
                        {{ \App\Constants\CarStatus::LABELS[$tabCar->status] ?? $tabCar->status }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- メインコンテンツ --}}
        @if(!$car)
            <p style="color: #9ca3af; text-align: center; padding: 40px;">車両が見つかりません</p>
        @else
        <div style="padding: 20px;">

            {{-- ステータスバー --}}
            @php
                $statusConfig = \App\Constants\CarStatus::STATUS_CONFIG[$car->status] ?? [
                    'label'       => \App\Constants\CarStatus::LABELS[$car->status] ?? $car->status,
                    'bg'          => '#f3f4f6',
                    'color'       => '#374151',
                    'border'      => '#d1d5db',
                    'icon'        => '',
                    'description' => '',
                ];
            @endphp
            <div style="background: {{ $statusConfig['bg'] }}; border: 1px solid {{ $statusConfig['border'] }}; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">{{ $statusConfig['icon'] }}</span>
                <div>
                    <p style="font-size: 13px; font-weight: 500; color: {{ $statusConfig['color'] }}; margin: 0;">
                        現在のステータス：{{ $statusConfig['label'] }}
                    </p>
                    @if(!empty($statusConfig['description']))
                    <p style="font-size: 11px; color: {{ $statusConfig['color'] }}; margin: 2px 0 0; opacity: 0.8;">
                        {{ $statusConfig['description'] }}
                    </p>
                    @endif
                </div>
            </div>

            {{-- 差し戻し内容がある場合 --}}
            @if($car->status === \App\Constants\CarStatus::REJECTED && !empty($reason))
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">

                {{-- 左：指摘内容 --}}
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0; padding-left: 10px; border-left: 3px solid #E24B4A;">指摘内容</p>

                    @if(!empty($generalComment))
                    <div style="background: white; border-radius: 10px; border: 0.5px solid #fca5a5; overflow: hidden;">
                        <div style="padding: 8px 14px; background: #fef2f2; border-bottom: 0.5px solid #fca5a5;">
                            <p style="font-size: 11px; font-weight: 500; color: #991b1b; margin: 0;">全体的な指摘</p>
                        </div>
                        <div style="padding: 12px 14px;">
                            <p style="font-size: 12px; color: #7f1d1d; margin: 0; line-height: 1.6;">{{ $generalComment }}</p>
                        </div>
                    </div>
                    @endif

                    @foreach($flaggedImages as $flaggedImg)
                    @php
                        $imgRecord  = \App\Infrastructure\Eloquent\User\StkCarImages::find($flaggedImg['id']);
                        $imgUrl     = $imgRecord ? Storage::disk('s3')->url($imgRecord->image_url) : null;
                        $imgPath    = $imgRecord?->image_url ?? '';
                        $fileName   = basename($imgPath);
                        $folderName = basename(dirname($imgPath));
                        $shortPath  = $folderName . '/' . $fileName;
                        $resolved   = $flaggedImg['resolved'] ?? false;
                    @endphp
                    <div style="background: white; border-radius: 10px; border: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }}; overflow: hidden;">
                        <div style="padding: 8px 14px; background: {{ $resolved ? '#f0fdf4' : '#fef2f2' }}; border-bottom: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }}; display: flex; align-items: center; gap: 8px;">
                            <p style="font-size: 11px; font-weight: 500; color: {{ $resolved ? '#15803d' : '#991b1b' }}; margin: 0;">画像の指摘</p>
                            @if($resolved)
                                <span style="font-size: 10px; background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 4px;">対応済み</span>
                            @endif
                        </div>
                        <div style="padding: 12px 14px; display: flex; gap: 12px; align-items: flex-start;">
                            @if($imgUrl)
                            <div style="flex-shrink: 0;">
                                <div style="width: 80px; height: 60px; border-radius: 6px; overflow: hidden; border: {{ $resolved ? '0.5px solid #bbf7d0' : '2px solid #E24B4A' }};">
                                    <img src="{{ $imgUrl }}" style="width: 100%; height: 100%; object-fit: cover;" />
                                </div>
                                <p style="font-size: 9px; color: #6b7280; margin: 4px 0 0; word-break: break-all; max-width: 80px;">{{ $fileName }}</p>
                            </div>
                            @endif
                            <div>
                                <p style="font-size: 12px; color: #7f1d1d; margin: 0 0 6px; line-height: 1.6;">{{ $flaggedImg['reason'] }}</p>
                                @if(!empty($imgPath))
                                <p style="font-size: 10px; color: #9ca3af; margin: 0; word-break: break-all; background: #f3f4f6; padding: 4px 6px; border-radius: 4px;">
                                    📁 {{ $shortPath }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @foreach($items as $idx => $item)
                    @php $resolved = $item['resolved'] ?? false; @endphp
                    <div style="background: white; border-radius: 10px; border: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }}; overflow: hidden;">
                        <div style="padding: 8px 14px; background: {{ $resolved ? '#f0fdf4' : '#fef2f2' }}; border-bottom: 0.5px solid {{ $resolved ? '#bbf7d0' : '#fca5a5' }}; display: flex; align-items: center; gap: 8px;">
                            <p style="font-size: 11px; font-weight: 500; color: {{ $resolved ? '#15803d' : '#991b1b' }}; margin: 0;">{{ $item['category'] ?? 'その他' }}</p>
                            @if($resolved)
                                <span style="font-size: 10px; background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 4px;">対応済み</span>
                            @endif
                        </div>
                        <div style="padding: 12px 14px;">
                            <p style="font-size: 12px; color: #7f1d1d; margin: 0; line-height: 1.6;">{{ $item['reason'] ?? '' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- 右：対応内容入力 --}}
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <p style="font-size: 12px; font-weight: 500; color: #374151; margin: 0; padding-left: 10px; border-left: 3px solid #185FA5;">対応内容を入力</p>

                    @if(!empty($generalComment))
                    <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 8px 14px; background: #f0f9ff; border-bottom: 0.5px solid #bae6fd;">
                            <p style="font-size: 11px; font-weight: 500; color: #0369a1; margin: 0;">全体指摘への対応</p>
                        </div>
                        <div style="padding: 12px 14px;">
                            <textarea
                                wire:model.lazy="generalResponses.{{ $currentCarId }}"
                                style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 12px; color: #111827; background: #f9fafb; resize: vertical; min-height: 72px;"
                                placeholder="対応した内容を入力してください"
                            ></textarea>
                        </div>
                    </div>
                    @endif

                    @foreach($flaggedImages as $flaggedImg)
                    @php $resolved = $flaggedImg['resolved'] ?? false; @endphp
                    <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 8px 14px; background: #f0f9ff; border-bottom: 0.5px solid #bae6fd;">
                            <p style="font-size: 11px; font-weight: 500; color: #0369a1; margin: 0;">画像指摘への対応</p>
                        </div>
                        <div style="padding: 12px 14px;">
                            @if($resolved)
                                <p style="font-size: 12px; color: #15803d; margin: 0; background: #f0fdf4; padding: 8px 10px; border-radius: 6px; border-left: 3px solid #22c55e;">
                                    {{ $flaggedImg['dealer_response'] ?? '対応済み' }}
                                </p>
                            @else
                                <input
                                    type="text"
                                    wire:model.lazy="imageResponses.{{ $currentCarId }}.{{ $flaggedImg['id'] }}"
                                    placeholder="例：撮り直してアップロードしました"
                                    style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 12px; color: #111827; background: #f9fafb;"
                                />
                            @endif
                        </div>
                    </div>
                    @endforeach

                    @foreach($items as $idx => $item)
                    @php $resolved = $item['resolved'] ?? false; @endphp
                    <div style="background: white; border-radius: 10px; border: 0.5px solid #e5e7eb; overflow: hidden;">
                        <div style="padding: 8px 14px; background: #f0f9ff; border-bottom: 0.5px solid #bae6fd;">
                            <p style="font-size: 11px; font-weight: 500; color: #0369a1; margin: 0;">{{ $item['category'] ?? 'その他' }}への対応</p>
                        </div>
                        <div style="padding: 12px 14px;">
                            @if($resolved)
                                <p style="font-size: 12px; color: #15803d; margin: 0; background: #f0fdf4; padding: 8px 10px; border-radius: 6px; border-left: 3px solid #22c55e;">
                                    {{ $item['dealer_response'] ?? '対応済み' }}
                                </p>
                            @else
                                <textarea
                                    wire:model.lazy="itemResponses.{{ $currentCarId }}.{{ $idx }}"
                                    style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 12px; color: #111827; background: #f9fafb; resize: vertical; min-height: 60px;"
                                    placeholder="対応した内容を入力してください"
                                ></textarea>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    <button
                        type="button"
                        wire:click="saveDealerResponse"
                        style="width: 100%; padding: 12px; background: #185FA5; color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 500; cursor: pointer;"
                    >
                        対応内容を保存する
                    </button>
                </div>
            </div>

            @else
            <div style="background: #f0fdf4; border: 0.5px solid #bbf7d0; border-radius: 10px; padding: 20px; text-align: center;">
                <p style="font-size: 13px; color: #15803d; margin: 0;">この車両に差し戻し内容はありません</p>
            </div>
            @endif

        </div>
        @endif

    </div>{{-- タブ＋メインコンテンツ --}}

    {{-- ===== 再アップロードエリア ===== --}}
    <div>
    <x-bulk-upload-steps
            :upload-step="$uploadStep"
            :preview-summary="$previewSummary"
            :xlsx-errors="$xlsxErrors"
            :image-errors="$imageErrors"
            :validated-rows="$validatedRows"
            :uploaded-image-map="$uploadedImageMap"
            :imported-car-ids="$importedCarIds"
            title="再アップロード"
            alpine-data-name="imageUploaderDetail"
        />
    </div>{{-- 再アップロードエリア --}}

</x-filament-panels::page>