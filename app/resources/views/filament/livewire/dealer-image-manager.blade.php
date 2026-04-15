<div
    x-data="{
        dealerId:      @js($dealerId),
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

            window.Livewire.dispatchTo('dealer-image-manager', 'dealer-images-uploaded');
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
            formData.append('file',      file);
            formData.append('dealer_id', this.dealerId);
            const token = document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '';

            const response = await fetch('/api/DealerImages/upload', {
                method: 'POST',
                headers: { 'X-XSRF-TOKEN': decodeURIComponent(token) },
                body: formData,
            });

            const data = await response.json();
            if (!data.success) throw new Error(data.message);
        },
    }"
>

    <div x-init="
        $watch('fileCount', val => { window.dealerImageFileCount = val; });
        window.dealerImageFileCount = 0;
    "></div>

    {{-- ===== ヘッダー ===== --}}
    <div style="padding: 14px 20px; border-bottom: 0.5px solid #e5e7eb;">
        <p style="font-size: 15px; font-weight: 500; margin: 0;">店舗画像管理</p>
        <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0;">{{ $dealerName }}</p>
    </div>

    {{-- ===== 画像追加エリア ===== --}}
    <div style="padding: 16px 20px; border-bottom: 0.5px solid #e5e7eb;">
        <p style="font-size: 13px; font-weight: 500; color: #6b7280; margin: 0 0 10px;">画像を追加</p>

        <div style="margin-bottom: 8px;">
            <label style="font-size: 11px; color: #9ca3af; display: block; margin-bottom: 4px;">
                画像ファイル（複数選択可・最大10枚）
            </label>
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

        {{-- 注意書き --}}
        <p style="
            margin-top: 10px;
            font-size: 13px;
            font-weight: 500;
            color: #dc2626;
            text-align: center;
        ">
            ⚠️ ファイル選択後、必ず「画像をアップロードする」ボタンを押してください。ボタンを押さないと画像は保存されません。
        </p>
        <button
            type="button"
            x-on:click="uploadAll"
            x-bind:disabled="uploading || fileCount === 0"
            x-bind:class="(uploading || fileCount === 0) ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'"
            style="
                margin-top: 12px;
                width: 100%;
                padding: 12px;
                background: #16a34a;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 500;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            "
        >
            <span style="font-size: 16px;">↑</span>
            <span x-show="!uploading">画像をアップロードする</span>

            <span x-show="uploading" x-text="'アップロード中 ' + uploadedCount + '/' + fileCount + ' 枚'"></span>
        </button>

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

    {{-- ===== 登録済み画像 ===== --}}
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
                id="sortable-dealer-images"
                style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 12px;"
                x-data="{
                    initSortable() {
                        Sortable.create(document.getElementById('sortable-dealer-images'), {
                            animation: 150,
                            handle: '.drag-handle',
                            onEnd: (evt) => {
                                const ids = Array.from(document.getElementById('sortable-dealer-images').children)
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
                    style="border: 0.5px solid #e5e7eb; border-radius: 10px; overflow: hidden; background: white;"
                >
                    {{-- 画像エリア --}}
                    <div style="position: relative;">

                        {{-- 画像 --}}
                        <div
                            wire:click="preview('{{ $image['url'] }}')"
                            style="height: 120px; overflow: hidden; cursor: pointer; background: #f3f4f6;"
                        >
                            <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" style="width: 100%; height: 100%; object-fit: cover;" />
                        </div>

                        {{-- 左上：チェックボックス＋ドラッグハンドル --}}
                        <div style="position: absolute; top: 6px; left: 6px; display: flex; align-items: center; gap: 4px;">
                            <div class="drag-handle" style="color: white; font-size: 12px; cursor: grab; padding: 1px 4px; background: rgba(0,0,0,0.4); border-radius: 3px; line-height: 1.4;">≡</div>
                            <input type="checkbox" value="{{ $image['id'] }}" wire:model="selectedIds" style="width: 14px; height: 14px; cursor: pointer;" />
                        </div>

                        {{-- 右上：メインバッジ or ☆ボタン --}}
                        <div style="position: absolute; top: 6px; right: 6px;">
                            @if($image['is_main'])
                                <span style="background: #FAC775; color: #633806; font-size: 9px; padding: 2px 6px; border-radius: 3px; font-weight: 500;">メイン</span>
                            @else
                                <button type="button" wire:click="setMain({{ $image['id'] }})" style="background: rgba(255,255,255,0.85); color: #d97706; font-size: 14px; border: none; border-radius: 3px; padding: 1px 5px; cursor: pointer;" title="メイン画像に設定">☆</button>
                            @endif
                        </div>
                    </div>

                    {{-- キャプションエリア --}}
                    <div style="padding: 8px 10px; background: white;">
                        @if($editingCaptionImageId === $image['id'])
                            <textarea
                                wire:model="editingCaption"
                                style="width: 100%; box-sizing: border-box; border: 0.5px solid #d1d5db; border-radius: 6px; padding: 6px 8px; font-size: 11px; color: #111827; background: #f9fafb; resize: vertical; min-height: 50px;"
                                placeholder="キャプションを入力"
                            ></textarea>
                            <div style="display: flex; gap: 4px; margin-top: 4px;">
                                <button type="button" wire:click="saveCaption" style="flex: 1; padding: 4px; background: #185FA5; color: white; border: none; border-radius: 6px; font-size: 11px; cursor: pointer;">保存</button>
                                <button type="button" wire:click="cancelEditCaption" style="flex: 1; padding: 4px; background: #f3f4f6; color: #6b7280; border: 0.5px solid #d1d5db; border-radius: 6px; font-size: 11px; cursor: pointer;">キャンセル</button>
                            </div>
                        @else
                            <p style="font-size: 11px; color: #6b7280; margin: 0 0 6px; min-height: 16px; word-break: break-all;">
                                {{ $image['caption'] ?: '（キャプションなし）' }}
                            </p>
                            <button type="button" wire:click="startEditCaption({{ $image['id'] }}, '{{ addslashes($image['caption']) }}')" style="font-size: 11px; color: #185FA5; background: #EFF6FF; border: 0.5px solid #bfdbfe; border-radius: 4px; padding: 2px 8px; cursor: pointer;">
                                ✏️ 編集
                            </button>
                        @endif
                    </div>
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
            </div>
        @else
            <p style="font-size: 13px; color: #9ca3af; text-align: center; padding: 24px 0;">画像が登録されていません</p>
        @endif
    </div>

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