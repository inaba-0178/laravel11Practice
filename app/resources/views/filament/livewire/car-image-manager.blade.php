<div
    x-data="{
        carId:         @js($carId),
        imageType:     'exterior',
        files:         [],
        fileCount:     0,
        uploading:     false,
        uploadedCount: 0,
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

            // 並列→直列に変更
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
                headers: {
                    'X-XSRF-TOKEN': decodeURIComponent(token),
                },
                body: formData,
            });

            const data = await response.json();

            if ( !data.success) {
                throw new Error(data.message);
            }
        },
    }"
>

    {{-- ===== ヘッダー ===== --}}
    <div style="padding: 16px 20px; border-bottom: 0.5px solid #e5e7eb;">
        <p style="font-size: 15px; font-weight: 500; margin: 0;">画像アップロード</p>
        <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0;">{{ $carLabel }}</p>
        <p style="font-size: 12px; color: #9ca3af; margin: 2px 0 0;">{{ $carSubLabel }}</p>
    </div>

    {{-- ===== 画像追加エリア ===== --}}
    <div style="padding: 16px 20px; border-bottom: 0.5px solid #e5e7eb; background: #1a1a1a; color: #fff;">
    <p style="font-size: 13px; font-weight: 500; color: #9ca3af; margin: 0 0 10px;">画像を追加</p>

    {{-- ファイル選択 --}}
    <div style="position: relative; border-radius: 16px; overflow: hidden; background: #262626; padding: 2px;">
        <label style="font-size: 11px; color: #9ca3af; display: block; margin-bottom: 4px;">
            画像ファイル（複数選択可）
        </label>
        <input
            type="file"
            x-ref="fileInput"
            multiple
            accept="image/*"
            x-on:change="onFileChange"
            style="display: block; width: 100%; font-size: 13px; color: #6b7280; border: 0.5px solid #374151; border-radius: 8px; padding: 7px 12px; background: #ffffff; box-sizing: border-box;"
        />
        <p x-show="fileCount > 0" style="font-size: 11px; color: #9ca3af; margin: 4px 0 0;">
            <span x-text="fileCount"></span>枚選択中
        </p>
    </div>

    {{-- 種別選択 --}}
    <div style="margin-bottom: 20px; width: 160px;">
        <label style="font-size: 11px; color: #9ca3af; display: block; margin-bottom: 4px;">画像種別</label>
        <select
            x-model="imageType"
            style="display: block; width: 100%; font-size: 13px; border: 0.5px solid #374151; border-radius: 8px; padding: 7px 12px; background: #ffffff; color: #111827; box-sizing: border-box;"
        >
            <option value="exterior">エクステリア</option>
            <option value="interior">インテリア</option>
            <option value="engine">エンジン</option>
            <option value="other">その他</option>
        </select>
    </div>

    {{-- アップロードボタン --}}
    <div style="margin-bottom: 20px;">
        <button
            type="button"
            x-on:click="uploadAll"
            x-bind:disabled="uploading || fileCount === 0"
            style="
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 10px 30px;
                background: #2D92B9; /* 画像に近い明るい青色 */
                color: #ffffff;
                border: none;
                border-radius: 12px;
                font-size: 18px;
                font-weight: bold;
                cursor: pointer;
                width: 100%;
                max-width: 320px;
                box-shadow: 0 4px 0 #1D6F8C; /* ボタンの下に少し厚みをつける */
                transition: transform 0.1s;
            "
            x-on:mousedown="$el.style.transform = 'translateY(2px)'; $el.style.boxShadow = 'none'"
            x-on:mouseup="$el.style.transform = 'translateY(0)'; $el.style.boxShadow = '0 4px 0 #1D6F8C'"
        >
            <!-- アップロードアイコン (SVG) -->

            <span x-show="!uploading" style="text-decoration: underline;">アップロード</span>
            <span x-show="uploading">アップロード中...</span>
        </button>
    </div>

    {{-- プログレス表示（画像に合わせたデザイン） --}}
    <div x-show="uploading" style="margin-top: 20px;">
        <div style="background: #ffffff; border-radius: 20px; padding: 30px 40px; width: 100%; max-width: 800px; box-sizing: border-box; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: center; align-items: baseline; gap: 15px; margin-bottom: 20px;">
                <span style="font-size: 28px; color: #374151; font-weight: 700; font-family: sans-serif;" 
                      x-text="'アップロード中 ' + uploadedCount + ' / ' + fileCount + ' 枚'"></span>
                <span style="font-size: 28px; color: #185FA5; font-weight: 700; font-family: sans-serif;" 
                      x-text="Math.min(Math.round((uploadedCount / fileCount) * 100), 100) + '%'"></span>
            </div>
            <div style="background: #e5e7eb; border-radius: 50px; height: 24px; overflow: hidden; width: 100%;">
                <div
                    x-bind:style="'width: ' + Math.min(Math.round((uploadedCount / fileCount) * 100), 100) + '%; background: #185FA5; height: 100%; border-radius: 50px; transition: width 0.3s ease;'"
                ></div>
            </div>
            <p x-show="failedCount > 0" x-text="'失敗: ' + failedCount + '枚'" style="font-size: 14px; color: #dc2626; margin: 12px 0 0; text-align: center;"></p>
        </div>
    </div>

    {{-- エラー表示 --}}
    <p x-show="errorMessage !== ''" x-text="errorMessage" style="font-size: 11px; color: #dc2626; margin: 10px 0 0;"></p>
</div>


    {{-- ===== 登録済み画像 ===== --}}
    <div style="padding: 16px 20px; border-bottom: 0.5px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <p style="font-size: 13px; font-weight: 500; color: #6b7280; margin: 0;">
                登録済み画像
                <span style="font-size: 11px; color: #9ca3af;">（{{ count($images) }}枚）</span>
            </p>
            <p style="font-size: 11px; color: #9ca3af; margin: 0;">クリックで拡大 / ★でメイン画像設定</p>
        </div>

        @if(count($images) > 0)
            <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; margin-bottom: 12px;">
                @foreach($images as $image)
                    <div style="position: relative;">
                        <div style="position: absolute; top: 3px; left: 3px; z-index: 1;">
                            <input
                                type="checkbox"
                                value="{{ $image['id'] }}"
                                wire:model="selectedIds"
                                style="width: 14px; height: 14px; border-radius: 3px; cursor: pointer;"
                            />
                        </div>

                        @if($image['is_main'])
                            <span style="position: absolute; top: 3px; right: 3px; background: #FAC775; color: #633806; font-size: 9px; padding: 1px 4px; border-radius: 3px; z-index: 1;">メイン</span>
                        @endif

                        <div
                            wire:click="preview('{{ $image['url'] }}')"
                            style="border-radius: 8px; border: {{ $image['is_main'] ? '2px solid #185FA5' : '0.5px solid #e5e7eb' }}; height: 72px; overflow: hidden; cursor: pointer; background: #f3f4f6;"
                        >
                            <img
                                src="{{ $image['url'] }}"
                                alt="{{ $image['type_label'] }}"
                                style="width: 100%; height: 100%; object-fit: cover;"
                            />
                        </div>

                        @if(!$image['is_main'])
                            <button
                                wire:click="setMain({{ $image['id'] }})"
                                style="position: absolute; bottom: 18px; right: 3px; background: rgba(255,255,255,0.85); color: #d97706; font-size: 11px; border: none; border-radius: 3px; padding: 1px 4px; cursor: pointer;"
                                title="メイン画像に設定"
                            >★</button>
                        @endif

                        <p style="font-size: 10px; color: #9ca3af; text-align: center; margin: 3px 0 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $image['type_label'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            <div style="display: flex; align-items: center; gap: 8px; padding-top: 10px; border-top: 0.5px solid #e5e7eb;">
                <span style="font-size: 12px; color: #9ca3af;">{{ count($selectedIds) }}件選択中</span>
                {{-- 選択削除 --}}
                <button
                    type="button"
                    wire:click="confirmDelete"
                    x-bind:disabled="$wire.selectedIds.length === 0"
                    x-bind:style="$wire.selectedIds.length === 0 
                        ? 'opacity: 0.4; cursor: not-allowed; padding: 5px 12px; font-size: 12px; border: 0.5px solid #fca5a5; color: #dc2626; background: #fef2f2; border-radius: 8px;' 
                        : 'padding: 5px 12px; font-size: 12px; border: 0.5px solid #fca5a5; color: #dc2626; background: #fef2f2; border-radius: 8px; cursor: pointer;'"
                >選択削除</button>
                {{-- 全件削除 --}}
                <button
                    type="button"
                    wire:click="confirmDeleteAll"
                    style="padding: 5px 12px; font-size: 12px; border: 0.5px solid #dc2626; color: white; background: #dc2626; border-radius: 8px; cursor: pointer;"
                >全件削除</button>
            </div>
        @else
            <p style="font-size: 13px; color: #9ca3af; text-align: center; padding: 24px 0;">画像が登録されていません</p>
        @endif
    </div>

    {{-- ===== 拡大表示 ===== --}}
    @if($previewUrl)
        <div
            x-on:click="$wire.closePreview()"
            style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.75); cursor: pointer;"
        >
            <div style="position: relative; max-width: 90vw; max-height: 90vh; padding: 16px;" wire:click.stop>
                <img src="{{ $previewUrl }}" style="max-width: 100%; max-height: 80vh; border-radius: 8px; object-fit: contain;" />
                <button
                    type="button"
                    x-on:click="$wire.closePreview()"
                    style="position: absolute; top: 4px; right: 4px; background: rgba(255,255,255,0.85); border: none; border-radius: 50%; width: 28px; height: 28px; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                >✕</button>
            </div>
        </div>
    @endif

    {{-- ===== 選択削除確認モーダル ===== --}}
    @if($showDeleteConfirm)
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
            <div style="background: white; border-radius: 12px; padding: 24px; max-width: 360px; width: 100%; margin: 0 16px;">
                <p style="font-size: 15px; font-weight: 500; margin: 0 0 8px;">選択した画像を削除しますか？</p>
                <p style="font-size: 13px; color: #6b7280; margin: 0 0 16px;">{{ count($selectedIds) }}枚の画像を削除します。削除した画像は元に戻せませんがよろしいですか？</p>
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" wire:click="cancelDelete" style="padding: 7px 16px; font-size: 13px; border: 0.5px solid #d1d5db; background: #f9fafb; color: #374151; border-radius: 8px; cursor: pointer;">キャンセル</button>
                    <button type="button" wire:click="deleteSelected" style="padding: 7px 16px; font-size: 13px; border: none; background: #dc2626; color: white; border-radius: 8px; cursor: pointer;">削除する</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== 全件削除確認モーダル ===== --}}
    @if($showDeleteAllConfirm)
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);">
            <div style="background: white; border-radius: 12px; padding: 24px; max-width: 360px; width: 100%; margin: 0 16px;">
                <p style="font-size: 15px; font-weight: 500; margin: 0 0 8px;">全ての画像を削除しますか？</p>
                <p style="font-size: 13px; color: #6b7280; margin: 0 0 16px;">登録済みの全画像（{{ count($images) }}枚）を削除します。削除した画像は元に戻せませんがよろしいですか？</p>
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