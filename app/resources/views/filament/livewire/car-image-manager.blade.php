<div
    x-data="{
        carId:         @js($carId),
        imageType:     'exterior',
        files:         [],
        fileCount:     0,
        uploading:     false,
        uploadedCount: 0,
        failedCount:   0,
        errorMessage:  '',

        onFileChange(e) {
            this.files        = Array.from(e.target.files);
            this.fileCount    = this.files.length;
            this.errorMessage = '';
        },

        async uploadAll() {
            if (!this.files.length) return;
            this.uploading     = true;
            this.uploadedCount = 0;
            this.failedCount   = 0;
            this.errorMessage  = '';

            for (const file of this.files) {
                try {
                    await this.uploadOne(file);
                    this.uploadedCount++;
                } catch (e) {
                    this.failedCount++;
                }
            }

            window.Livewire.dispatchTo('car-image-manager', 'images-uploaded');
            this.$refs.fileInput.value = '';
            this.files     = [];
            this.fileCount = 0;
            this.uploading = false;

            if (this.failedCount > 0) {
                this.errorMessage = this.failedCount + '枚のアップロードに失敗しました。';
            }
        },

        async uploadOne(file) {
            const formData = new FormData();
            formData.append('file',       file);
            formData.append('car_id',     this.carId);
            formData.append('image_type', this.imageType);
            const token = document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '';

            const response = await fetch('/api/CarImages/upload', {
                method: 'POST',
                headers: { 'X-XSRF-TOKEN': decodeURIComponent(token) },
                body: formData,
            });

            const data = await response.json();
            if (!data.success) throw new Error(data.message);
        },
    }"
>

    {{-- ===== ヘッダー ===== --}}
    <div style="padding: 14px 20px; border-bottom: 0.5px solid #e5e7eb;">
        <p style="font-size: 15px; font-weight: 500; margin: 0;">画像アップロード</p>
        <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0;">{{ $carLabel }}</p>
        <p style="font-size: 12px; color: #9ca3af; margin: 2px 0 0;">{{ $carSubLabel }}</p>
    </div>

    {{-- ===== 差し戻し対応入力エリア（pendingモード） ===== --}}
    @if($viewMode === 'pending' && count($pendingResponses) > 0)
    <div style="padding: 16px 20px; border-bottom: 0.5px solid #e5e7eb; background: #fffbeb;">

        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
            <span style="background: #E24B4A; color: white; font-size: 11px; padding: 2px 8px; border-radius: 5px; font-weight: 500;">要対応</span>
            <p style="font-size: 13px; font-weight: 500; color: #92400e; margin: 0;">削除した指摘画像への対応を入力してください</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px;">
            @foreach($pendingResponses as $idx => $pending)
            <div style="background: white; border-radius: 10px; border: 0.5px solid #fca5a5; overflow: hidden;">

                {{-- 指摘内容 --}}
                <div style="padding: 10px 14px; background: #fef2f2; border-bottom: 0.5px solid #fca5a5;">
                    <p style="font-size: 11px; font-weight: 500; color: #991b1b; margin: 0 0 2px;">指摘内容</p>
                    <p style="font-size: 12px; color: #7f1d1d; margin: 0;">{{ $pending['reason'] }}</p>
                </div>

                <div style="padding: 12px 14px; display: flex; flex-direction: column; gap: 12px;">

                    {{-- 差し替え画像アップロード --}}
                    <div>
                        <label style="font-size: 11px; color: #6b7280; display: block; margin-bottom: 6px;">
                            差し替え画像（任意）
                        </label>
                        <input
                            type="file"
                            x-ref="replacementFile_{{ $idx }}"
                            accept="image/*"
                            style="display: block; width: 100%; font-size: 12px; color: #6b7280; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 6px 10px; background: #f9fafb; box-sizing: border-box;"
                        />
                    </div>

                    {{-- 対応内容テキスト --}}
                    <div>
                        <label style="font-size: 11px; color: #6b7280; display: block; margin-bottom: 4px;">対応内容を入力 <span style="color: #dc2626;">*</span></label>
                        <textarea
                            wire:model.live="pendingResponses.{{ $idx }}.response"
                            style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 8px 10px; font-size: 12px; color: #111827; background: #f9fafb; resize: vertical; min-height: 60px;"
                            placeholder="例：撮り直して差し替え画像をアップロードしました"
                        ></textarea>
                    </div>

                    {{-- 対応完了ボタン --}}
                    <div x-data="{ uploading: false }">
                        <button
                            type="button"
                            x-on:click="
                                uploading = true;
                                const file = $refs['replacementFile_{{ $idx }}']?.files[0];

                                const doComplete = async (imageId = null) => {
                                    await $wire.completeResponse({{ $idx }}, imageId);
                                    uploading = false;
                                };

                                if (file) {
                                    const formData = new FormData();
                                    formData.append('file', file);
                                    formData.append('car_id', {{ $carId }});
                                    formData.append('image_type', 'exterior');
                                    const token = document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '';
                                    fetch('/api/CarImages/upload', {
                                        method: 'POST',
                                        headers: { 'X-XSRF-TOKEN': decodeURIComponent(token) },
                                        body: formData,
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (data.success) {
                                            doComplete(data.carImage.id);
                                        } else {
                                            alert('画像のアップロードに失敗しました');
                                            uploading = false;
                                        }
                                    })
                                    .catch(() => { alert('通信エラーが発生しました'); uploading = false; });
                                } else {
                                    doComplete(null);
                                }
                            "
                            x-bind:disabled="uploading"
                            x-bind:style="uploading ? 'opacity: 0.6; cursor: not-allowed; width: 100%; padding: 10px; background: #3B6D11; color: #EAF3DE; border: none; border-radius: 8px; font-size: 13px; font-weight: 500;' : 'width: 100%; padding: 10px; background: #3B6D11; color: #EAF3DE; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;'"
                        >
                            <span x-show="!uploading">対応完了</span>
                            <span x-show="uploading">処理中...</span>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- 画像一覧に戻るリンク --}}
        <button
            type="button"
            wire:click="$set('viewMode', 'grid')"
            style="margin-top: 12px; width: 100%; padding: 8px; background: none; border: 0.5px solid #d1d5db; border-radius: 8px; font-size: 12px; color: #6b7280; cursor: pointer;"
        >
            ← 画像一覧に戻る
        </button>
    </div>
    @endif

    {{-- ===== 画像追加エリア（gridモードのみ） ===== --}}
    @if($viewMode === 'grid')
    <div style="padding: 16px 20px; border-bottom: 0.5px solid #e5e7eb;">
        <p style="font-size: 13px; font-weight: 500; color: #6b7280; margin: 0 0 10px;">画像を追加</p>

        <div style="margin-bottom: 8px;">
            <label style="font-size: 11px; color: #9ca3af; display: block; margin-bottom: 4px;">画像ファイル（複数選択可）</label>
            <input
                type="file"
                x-ref="fileInput"
                multiple
                accept="image/*"
                x-on:change="onFileChange"
                style="display: block; width: 100%; font-size: 13px; color: #6b7280; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 7px 12px; background: #f9fafb; box-sizing: border-box;"
            />
            <p x-show="fileCount > 0" style="font-size: 11px; color: #6b7280; margin: 4px 0 0;">
                <span x-text="fileCount"></span>枚選択中
            </p>
        </div>

        <div style="display: flex; gap: 8px; align-items: flex-end;">
            <div style="width: 160px;">
                <label style="font-size: 11px; color: #9ca3af; display: block; margin-bottom: 4px;">画像種別</label>
                <select x-model="imageType" style="display: block; width: 100%; font-size: 13px; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 7px 12px; background: #f9fafb; color: #111827; box-sizing: border-box;">
                    <option value="exterior">エクステリア</option>
                    <option value="interior">インテリア</option>
                    <option value="engine">エンジン</option>
                    <option value="other">その他</option>
                </select>
            </div>

            <button
                type="button"
                x-on:click="uploadAll"
                x-bind:disabled="uploading || fileCount === 0"
                x-bind:style="(uploading || fileCount === 0) ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;'"
                style="padding: 8px 16px; background: #185FA5; color: #E6F1FB; border: none; border-radius: 8px; font-size: 13px; white-space: nowrap; height: 36px;"
            >
                <span x-show="!uploading">アップロード</span>
                <span x-show="uploading" x-text="'アップロード中 ' + uploadedCount + '/' + fileCount"></span>
            </button>
        </div>

        {{-- プログレスバー --}}
        <div x-show="uploading" style="margin-top: 10px;">
            <div style="background: #f9fafb; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 12px 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 13px; color: #374151; font-weight: 500;" x-text="'アップロード中 ' + uploadedCount + ' / ' + fileCount + ' 枚'"></span>
                    <span style="font-size: 13px; color: #185FA5; font-weight: 500;" x-text="Math.min(Math.round((uploadedCount / fileCount) * 100), 100) + '%'"></span>
                </div>
                <div style="background: #e5e7eb; border-radius: 6px; height: 10px; overflow: hidden;">
                    <div x-bind:style="'width: ' + Math.min(Math.round((uploadedCount / fileCount) * 100), 100) + '%; background: #185FA5; height: 100%; border-radius: 6px; transition: width 0.3s ease;'"></div>
                </div>
                <p x-show="failedCount > 0" x-text="'失敗: ' + failedCount + '枚'" style="font-size: 12px; color: #dc2626; margin: 6px 0 0;"></p>
            </div>
        </div>

        <p x-show="errorMessage !== ''" x-text="errorMessage" style="font-size: 11px; color: #dc2626; margin: 6px 0 0;"></p>
    </div>
    @endif

    {{-- ===== 登録済み画像（gridモードのみ） ===== --}}
    @if($viewMode === 'grid')
    <div style="padding: 16px 20px; border-bottom: 0.5px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <p style="font-size: 13px; font-weight: 500; color: #6b7280; margin: 0;">
                登録済み画像
                <span style="font-size: 11px; color: #9ca3af;">（{{ count($images) }}枚）</span>
            </p>
            <p style="font-size: 11px; color: #9ca3af; margin: 0;">≡ ドラッグで並び替え可 / ★でメイン設定</p>
        </div>

        @if(count($images) > 0)
            <div
                id="sortable-images"
                style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; margin-bottom: 12px;"
                x-data="{
                    initSortable() {
                        Sortable.create(document.getElementById('sortable-images'), {
                            animation: 150,
                            handle: '.drag-handle',
                            onEnd: (evt) => {
                                const ids = Array.from(document.getElementById('sortable-images').children)
                                    .map(el => parseInt(el.dataset.imageId));
                                $wire.reorder(ids);
                            }
                        });
                    }
                }"
                x-init="
                    if (typeof Sortable === 'undefined') {
                        const s = document.createElement('script');
                        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js';
                        s.onload = () => initSortable();
                        document.head.appendChild(s);
                    } else {
                        initSortable();
                    }
                "
            >
                @foreach($images as $image)
                <div
                    data-image-id="{{ $image['id'] }}"
                    style="position: relative;"
                    x-data="{ showTip: false }"
                    x-on:mouseenter="showTip = true"
                    x-on:mouseleave="showTip = false"
                >
                    {{-- ドラッグハンドル --}}
                    <div class="drag-handle" style="position: absolute; top: 3px; left: 3px; z-index: 2; background: rgba(0,0,0,0.45); color: white; font-size: 10px; border-radius: 3px; padding: 1px 4px; cursor: grab; line-height: 1.4;">≡</div>

                    {{-- チェックボックス --}}
                    <div style="position: absolute; top: 3px; left: 22px; z-index: 2;">
                        <input type="checkbox" value="{{ $image['id'] }}" wire:model="selectedIds" style="width: 14px; height: 14px; border-radius: 3px; cursor: pointer;" />
                    </div>

                    {{-- メインバッジ --}}
                    @if($image['is_main'])
                        <span style="position: absolute; top: 3px; right: 3px; background: #FAC775; color: #633806; font-size: 9px; padding: 1px 4px; border-radius: 3px; z-index: 2;">メイン</span>
                    @endif

                    {{-- 指摘マーク --}}
                    @if($image['is_flagged'])
                        <span style="position: absolute; top: 3px; right: 3px; background: #E24B4A; color: white; font-size: 9px; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 500; z-index: 2;">!</span>
                    @endif

                    {{-- 差し替え済みバッジ --}}
                    @if($image['is_replacement'])
                        <span style="position: absolute; bottom: 18px; left: 3px; background: #22c55e; color: white; font-size: 8px; padding: 1px 4px; border-radius: 3px; z-index: 2;">対応済</span>
                    @endif

                    {{-- 画像 --}}
                    <div
                        wire:click="preview('{{ $image['url'] }}')"
                        style="border-radius: 8px;
                            border: {{ $image['is_replacement'] ? '2px solid #22c55e' : ($image['is_flagged'] ? '2px solid #E24B4A' : ($image['is_main'] ? '2px solid #185FA5' : '0.5px solid #e5e7eb')) }};
                            height: 72px; overflow: hidden; cursor: pointer; background: #f3f4f6;"
                    >
                        <img src="{{ $image['url'] }}" alt="{{ $image['type_label'] }}" style="width: 100%; height: 100%; object-fit: cover;" />
                    </div>

                    {{-- ツールチップ（指摘理由） --}}
                    @if($image['is_flagged'])
                    <div
                        x-show="showTip"
                        x-transition
                        style="position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%); background: #1f2937; color: white; font-size: 13px; padding: 10px 14px; border-radius: 8px; z-index: 50; pointer-events: none; min-width: 160px; max-width: 260px; white-space: normal; line-height: 1.6;"
                    >
                        <p style="font-size: 11px; color: #9ca3af; margin: 0 0 4px;">指摘内容</p>
                        {{ $image['flag_reason'] }}
                        <div style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); border: 6px solid transparent; border-top-color: #1f2937;"></div>
                    </div>
                    @endif

                    {{-- ★ボタン --}}
                    @if(!$image['is_main'])
                        <button type="button" wire:click="setMain({{ $image['id'] }})" style="position: absolute; bottom: 18px; right: 3px; background: rgba(255,255,255,0.85); color: #d97706; font-size: 11px; border: none; border-radius: 3px; padding: 1px 4px; cursor: pointer;" title="メイン画像に設定">★</button>
                    @endif

                    <p style="font-size: 10px; color: #9ca3af; text-align: center; margin: 3px 0 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $image['type_label'] }}
                    </p>
                </div>
                @endforeach
            </div>

            <div style="display: flex; align-items: center; gap: 8px; padding-top: 10px; border-top: 0.5px solid #e5e7eb;">
                <span style="font-size: 12px; color: #9ca3af;">{{ count($selectedIds) }}件選択中</span>
                <button
                    type="button"
                    wire:click="confirmDelete"
                    x-bind:disabled="$wire.selectedIds.length === 0"
                    x-bind:style="$wire.selectedIds.length === 0
                        ? 'opacity: 0.4; cursor: not-allowed; padding: 5px 12px; font-size: 12px; border: 0.5px solid #fca5a5; color: #dc2626; background: #fef2f2; border-radius: 8px;'
                        : 'padding: 5px 12px; font-size: 12px; border: 0.5px solid #fca5a5; color: #dc2626; background: #fef2f2; border-radius: 8px; cursor: pointer;'"
                >選択削除</button>
                <button type="button" wire:click="confirmDeleteAll" style="padding: 5px 12px; font-size: 12px; border: 0.5px solid #dc2626; color: white; background: #dc2626; border-radius: 8px; cursor: pointer;">全件削除</button>

                @if(count($pendingResponses) > 0)
                <button type="button" wire:click="$set('viewMode', 'pending')" style="margin-left: auto; padding: 5px 12px; font-size: 12px; border: 0.5px solid #E24B4A; color: #E24B4A; background: #fef2f2; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                    <span style="background: #E24B4A; color: white; font-size: 9px; width: 16px; height: 16px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 500;">{{ count($pendingResponses) }}</span>
                    差し戻し対応へ
                </button>
                @endif
            </div>
        @else
            <p style="font-size: 13px; color: #9ca3af; text-align: center; padding: 24px 0;">画像が登録されていません</p>
        @endif
    </div>
    @endif

    {{-- ===== 拡大表示 ===== --}}
    @if($previewUrl)
        <div x-on:click="$wire.closePreview()" style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.75); cursor: pointer;">
            <div style="position: relative; max-width: 90vw; max-height: 90vh; padding: 16px;" wire:click.stop>
                <img src="{{ $previewUrl }}" style="max-width: 100%; max-height: 80vh; border-radius: 8px; object-fit: contain;" />
                <button type="button" x-on:click="$wire.closePreview()" style="position: absolute; top: 4px; right: 4px; background: rgba(255,255,255,0.85); border: none; border-radius: 50%; width: 28px; height: 28px; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center;">✕</button>
            </div>
        </div>
    @endif

    {{-- ===== 選択削除確認 ===== --}}
    @if($showDeleteConfirm)
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
            <div style="background: white; border-radius: 12px; padding: 24px; max-width: 360px; width: 100%; margin: 0 16px;">
                <p style="font-size: 15px; font-weight: 500; margin: 0 0 8px;">選択した画像を削除しますか？</p>
                <p style="font-size: 13px; color: #6b7280; margin: 0 0 16px;">{{ count($selectedIds) }}枚の画像を削除します。この操作は取り消せません。</p>
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" wire:click="cancelDelete" style="padding: 7px 16px; font-size: 13px; border: 0.5px solid #d1d5db; background: #f9fafb; color: #374151; border-radius: 8px; cursor: pointer;">キャンセル</button>
                    <button type="button" wire:click="deleteSelected" style="padding: 7px 16px; font-size: 13px; border: none; background: #dc2626; color: white; border-radius: 8px; cursor: pointer;">削除する</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== 全件削除確認 ===== --}}
    @if($showDeleteAllConfirm)
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
            <div style="background: white; border-radius: 12px; padding: 24px; max-width: 360px; width: 100%; margin: 0 16px;">
                <p style="font-size: 15px; font-weight: 500; margin: 0 0 8px;">全ての画像を削除しますか？</p>
                <p style="font-size: 13px; color: #6b7280; margin: 0 0 16px;">登録済みの全画像（{{ count($images) }}枚）を削除します。この操作は取り消せません。</p>
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" wire:click="cancelDelete" style="padding: 7px 16px; font-size: 13px; border: 0.5px solid #d1d5db; background: #f9fafb; color: #374151; border-radius: 8px; cursor: pointer;">キャンセル</button>
                    <button type="button" wire:click="deleteAll" style="padding: 7px 16px; font-size: 13px; border: none; background: #dc2626; color: white; border-radius: 8px; cursor: pointer;">全て削除する</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== トースト通知 ===== --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition
        style="position: fixed; bottom: 80px; right: 16px; z-index: 60; padding: 10px 16px; border-radius: 8px; font-size: 13px; color: white; pointer-events: none;"
        :style="type === 'success' ? 'background: #16a34a;' : 'background: #dc2626;'"
    >
        <span x-text="message"></span>
    </div>

</div>