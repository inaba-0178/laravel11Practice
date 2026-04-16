<div
    x-data="{
        type:         @js($type),
        recordId:     @js($recordId),
        file:         null,
        fileName:     '',
        uploading:    false,
        errorMessage: '',

        onFileChange(e) {
            const selected    = e.target.files[0];
            this.file         = selected;
            this.fileName     = selected ? selected.name : '';
            this.errorMessage = '';
        },

        async uploadImage() {
            if (!this.file) return;
            this.uploading    = true;
            this.errorMessage = '';

            try {
                const formData = new FormData();
                formData.append('type',      this.type);
                formData.append('record_id', this.recordId);
                formData.append('file',      this.file);
                const token = document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '';

                const response = await fetch('/api/Assets/upload', {
                    method: 'POST',
                    headers: { 'X-XSRF-TOKEN': decodeURIComponent(token) },
                    body: formData,
                });

                const data = await response.json();
                if (!data.success) throw new Error(data.message);

                window.Livewire.dispatchTo('asset-image-manager', 'asset-image-uploaded');
                this.$refs.fileInput.value = '';
                this.file     = null;
                this.fileName = '';

            } catch (e) {
                this.errorMessage = 'アップロードに失敗しました。';
            } finally {
                this.uploading = false;
            }
        },
    }"
>

    {{-- ===== ヘッダー ===== --}}
    <div style="padding: 14px 20px; border-bottom: 0.5px solid #e5e7eb;">
        <p style="font-size: 15px; font-weight: 500; margin: 0;">画像管理</p>
        <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0;">{{ $label }}</p>
    </div>

    {{-- ===== 現在の画像 ===== --}}
    <div style="padding: 16px 20px; border-bottom: 0.5px solid #e5e7eb;">
        <p style="font-size: 13px; font-weight: 500; color: #6b7280; margin: 0 0 10px;">現在の画像</p>

        @if($imageUrl)
            <div style="position: relative; width: 200px;">
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $label }}"
                    style="width: 200px; height: 150px; object-fit: cover; border-radius: 8px; border: 0.5px solid #e5e7eb;"
                />
                <button
                    type="button"
                    wire:click="confirmDelete"
                    style="position: absolute; top: 4px; right: 4px; background: #dc2626; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                >✕</button>
            </div>
        @else
            <div style="width: 200px; height: 150px; background: #f3f4f6; border-radius: 8px; border: 0.5px solid #e5e7eb; display: flex; align-items: center; justify-content: center;">
                <p style="font-size: 12px; color: #9ca3af; margin: 0;">NO IMAGE</p>
            </div>
        @endif
    </div>

    {{-- ===== 画像アップロード ===== --}}
    <div style="padding: 16px 20px; border-bottom: 0.5px solid #e5e7eb;">
        <p style="font-size: 13px; font-weight: 500; color: #6b7280; margin: 0 0 10px;">画像をアップロード</p>

        <input
            type="file"
            x-ref="fileInput"
            accept="image/*"
            x-on:change="onFileChange"
            style="display: block; width: 100%; font-size: 13px; color: #6b7280; border: 0.5px solid #d1d5db; border-radius: 8px; padding: 7px 12px; background: #f9fafb; box-sizing: border-box;"
        />

        <p x-show="fileName !== ''" style="font-size: 11px; color: #6b7280; margin: 4px 0 0;">
            選択中: <span x-text="fileName"></span>
        </p>

        <button
            type="button"
            x-on:click="uploadImage"
            x-bind:disabled="uploading || !file"
            x-bind:class="(uploading || !file) ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'"
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
            <span x-show="uploading">アップロード中...</span>
        </button>

        <p style="margin-top: 10px; font-size: 13px; font-weight: 500; color: #dc2626; text-align: center;">
            ⚠️ ファイル選択後、必ず「画像をアップロードする」ボタンを押してください。
        </p>

        <p x-show="errorMessage !== ''" x-text="errorMessage" style="font-size: 11px; color: #dc2626; margin: 6px 0 0;"></p>
    </div>

    {{-- ===== 削除確認モーダル ===== --}}
    @if($showDeleteConfirm)
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
            <div style="background: white; border-radius: 12px; padding: 24px; max-width: 360px; width: 100%; margin: 0 16px;">
                <p style="font-size: 15px; font-weight: 500; margin: 0 0 8px;">画像を削除しますか？</p>
                <p style="font-size: 13px; color: #6b7280; margin: 0 0 16px;">削除すると元に戻せません。</p>
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" wire:click="cancelDelete" style="padding: 7px 16px; font-size: 13px; border: 0.5px solid #d1d5db; background: #f9fafb; color: #374151; border-radius: 8px; cursor: pointer;">キャンセル</button>
                    <button type="button" wire:click="deleteImage" style="padding: 7px 16px; font-size: 13px; border: none; background: #dc2626; color: white; border-radius: 8px; cursor: pointer;">削除する</button>
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