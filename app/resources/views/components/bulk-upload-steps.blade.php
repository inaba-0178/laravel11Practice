@props([
    'uploadStep',
    'previewSummary'  => [],
    'xlsxErrors'      => [],
    'imageErrors'     => [],
    'validatedRows'   => [],
    'uploadedImageMap'=> [],
    'importedCarIds'  => [],
    'title'           => 'アップロード',
    'alpineDataName'  => 'imageUploader',
])

<div style="background: white; border-radius: 12px; border: 0.5px solid #e5e7eb; overflow: hidden;">
    <div style="padding: 12px 18px; background: #f9fafb; border-bottom: 0.5px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
        <p style="font-size: 13px; font-weight: 600; color: #374151; margin: 0;">🔄 {{ $title }}</p>
        @if($uploadStep !== 'STEP1')
        <button
            type="button"
            wire:click="resetUpload"
            style="font-size: 11px; color: #6b7280; background: none; border: 0.5px solid #d1d5db; border-radius: 6px; padding: 4px 10px; cursor: pointer;"
        >
            最初からやり直す
        </button>
        @endif
    </div>
    <div style="padding: 20px;">

        {{-- ステップインジケーター --}}
        @php
            $uploadSteps = [
                'STEP1' => ['label' => 'STEP 1', 'sub' => 'xlsx アップロード'],
                'STEP2' => ['label' => 'STEP 2', 'sub' => '画像アップロード'],
                'STEP3' => ['label' => 'STEP 3', 'sub' => '確認・承認依頼'],
                'DONE'  => ['label' => '完了',   'sub' => '承認依頼送信済み'],
            ];
            $stepOrder  = ['STEP1', 'STEP2', 'STEP3', 'DONE'];
            $currentIdx = array_search($uploadStep, $stepOrder);
        @endphp
        <div style="display: flex; align-items: center; gap: 0; margin-bottom: 24px;">
            @foreach($uploadSteps as $key => $s)
                @php
                    $idx       = array_search($key, $stepOrder);
                    $isDone    = $idx < $currentIdx;
                    $isCurrent = $key === $uploadStep;
                    $bg        = $isDone ? '#185FA5' : ($isCurrent ? '#2E75B6' : '#e5e7eb');
                    $color     = ($isDone || $isCurrent) ? '#fff' : '#6b7280';
                @endphp
                <div style="display: flex; align-items: center; flex: 1;">
                    <div style="flex: 1; text-align: center; padding: 8px 6px; background: {{ $bg }}; border-radius: 6px;">
                        <p style="margin: 0; font-size: 10px; font-weight: 700; color: {{ $color }};">{{ $s['label'] }}</p>
                        <p style="margin: 2px 0 0; font-size: 9px; color: {{ $color }}; opacity: 0.85;">{{ $s['sub'] }}</p>
                    </div>
                    @if(!$loop->last)
                        <div style="width: 16px; height: 2px; background: {{ $isDone ? '#185FA5' : '#e5e7eb' }};"></div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- STEP1: xlsx --}}
        @if($uploadStep === 'STEP1')
        <div
            x-data="{
                dragging: false,
                uploading: false,
                filename: '',
                handleFile(file) {
                    if (!file) return;
                    if (!file.name.endsWith('.xlsx')) {
                        alert('xlsxファイルを選択してください。');
                        return;
                    }
                    this.filename  = file.name;
                    this.uploading = true;
                    const reader = new FileReader();
                    reader.onload = async (e) => {
                        const base64 = e.target.result.split(',')[1];
                        await $wire.uploadXlsx(base64, file.name);
                        this.uploading = false;
                    };
                    reader.readAsDataURL(file);
                }
            }"
        >
            <div
                @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false"
                @drop.prevent="dragging = false; handleFile($event.dataTransfer.files[0])"
                @click="$refs.xlsxInputRef.click()"
                :style="dragging ? 'border: 2px dashed #2E75B6; border-radius: 10px; padding: 30px 24px; text-align: center; cursor: pointer; background: #EFF6FF;' : 'border: 2px dashed #d1d5db; border-radius: 10px; padding: 30px 24px; text-align: center; cursor: pointer;'"
            >
                <input type="file" x-ref="xlsxInputRef" accept=".xlsx" style="display: none;"
                    @change="handleFile($event.target.files[0])">
                <template x-if="!uploading && !filename">
                    <div>
                        <p style="font-size: 28px; margin: 0 0 6px;">📂</p>
                        <p style="font-size: 13px; color: #374151; margin: 0 0 4px;">クリックまたはドラッグ＆ドロップ</p>
                        <p style="font-size: 11px; color: #9ca3af; margin: 0;">.xlsx形式のみ対応</p>
                    </div>
                </template>
                <template x-if="uploading">
                    <p style="font-size: 13px; color: #2E75B6; margin: 0;">バリデーション中...</p>
                </template>
                <template x-if="!uploading && filename">
                    <p style="font-size: 13px; color: #374151; margin: 0;">📄 <span x-text="filename"></span></p>
                </template>
            </div>

            @if(!empty($xlsxErrors))
            <div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 16px; margin-top: 16px;">
                <p style="font-size: 13px; font-weight: 600; color: #991b1b; margin: 0 0 10px;">⚠️ バリデーションエラー（{{ count($xlsxErrors) }}件）</p>
                <div style="max-height: 240px; overflow-y: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                        <thead>
                            <tr style="background: #fee2e2;">
                                <th style="padding: 5px 8px; text-align: left; color: #991b1b; border-bottom: 1px solid #fca5a5;">行</th>
                                <th style="padding: 5px 8px; text-align: left; color: #991b1b; border-bottom: 1px solid #fca5a5;">列</th>
                                <th style="padding: 5px 8px; text-align: left; color: #991b1b; border-bottom: 1px solid #fca5a5;">エラー内容</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($xlsxErrors as $error)
                            <tr style="border-bottom: 1px solid #fee2e2;">
                                <td style="padding: 5px 8px; color: #7f1d1d;">{{ $error['row'] }}</td>
                                <td style="padding: 5px 8px; color: #7f1d1d;">{{ $error['column'] }}</td>
                                <td style="padding: 5px 8px; color: #7f1d1d;">{{ $error['message'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- STEP2: 画像アップロード --}}
        @if($uploadStep === 'STEP2')
        <div x-data="{{ $alpineDataName }}">
            <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px;">
                <p style="font-size: 12px; color: #0369a1; margin: 0;">
                    登録予定：<strong>{{ $previewSummary['total'] ?? 0 }}台</strong>　
                    必要フォルダ：<strong>{{ $previewSummary['folders'] ?? 0 }}個</strong>
                </p>
            </div>

            <div
                style="border: 2px dashed #d1d5db; border-radius: 10px; padding: 24px; text-align: center; cursor: pointer; margin-bottom: 14px;"
                @click="$refs.imgInputRef.click()"
            >
                <input
                    type="file"
                    x-ref="imgInputRef"
                    accept="image/*"
                    multiple
                    webkitdirectory
                    mozdirectory
                    style="display: none;"
                    @change="handleFiles($event)"
                >
                <p style="font-size: 28px; margin: 0 0 6px;">📁</p>
                <p style="font-size: 13px; color: #374151; margin: 0 0 4px;">クリックしてフォルダを選択</p>
                <p style="font-size: 11px; color: #9ca3af; margin: 0;">親フォルダ（bulkCar）を選択してください</p>
            </div>

            <template x-if="fileCount > 0">
                <p style="font-size: 12px; color: #374151; margin: 0 0 12px;">選択済み：<strong x-text="fileCount + '枚'"></strong></p>
            </template>

            <template x-if="uploading">
                <div style="margin-bottom: 14px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <p style="font-size: 12px; color: #374151; margin: 0;">アップロード中...</p>
                        <p style="font-size: 12px; color: #374151; margin: 0;" x-text="current + ' / ' + total + '枚'"></p>
                    </div>
                    <div style="background: #e5e7eb; border-radius: 4px; height: 8px;">
                        <div :style="'width: ' + progress + '%; background: #2E75B6; border-radius: 4px; height: 8px; transition: width 0.3s;'"></div>
                    </div>
                </div>
            </template>

            <div style="display: flex; gap: 10px;">
                <button
                    type="button"
                    @click="uploadAll($wire)"
                    :disabled="uploading || fileCount === 0"
                    :style="(uploading || fileCount === 0)
                        ? 'flex: 1; padding: 10px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: not-allowed; opacity: 0.5;'
                        : 'flex: 1; padding: 10px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;'"
                >
                    <span x-show="!uploading">アップロードして確認する</span>
                    <span x-show="uploading">アップロード中...</span>
                </button>
                <button
                    type="button"
                    wire:click="resetImageUpload"
                    style="padding: 10px 14px; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; font-size: 13px; cursor: pointer;"
                >
                    やり直す
                </button>
            </div>

            @if(!empty($imageErrors))
            <div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 14px; margin-top: 14px;">
                <p style="font-size: 13px; font-weight: 600; color: #991b1b; margin: 0 0 8px;">⚠️ 画像エラー（{{ count($imageErrors) }}件）</p>
                @foreach($imageErrors as $error)
                <div style="background: white; border-radius: 6px; padding: 8px 12px; margin-bottom: 6px; border-left: 3px solid #E24B4A;">
                    <p style="font-size: 11px; color: #7f1d1d; margin: 0;">📁 {{ $error['folder'] }}：{{ $error['message'] }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- STEP3: 確認・承認依頼 --}}
        @if($uploadStep === 'STEP3')
        <div>
            <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px;">
                <p style="font-size: 12px; color: #0369a1; margin: 0;">
                    登録予定：<strong>{{ $previewSummary['total'] ?? 0 }}台</strong>　
                    画像：<strong>{{ collect($uploadedImageMap)->sum(fn($paths) => count($paths)) }}枚</strong>
                </p>
            </div>

            <div style="max-height: 240px; overflow-y: auto; margin-bottom: 16px; border: 0.5px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead style="background: #f9fafb; position: sticky; top: 0;">
                        <tr>
                            <th style="padding: 8px 10px; text-align: left; color: #374151; border-bottom: 1px solid #e5e7eb;">操作</th>
                            <th style="padding: 8px 10px; text-align: left; color: #374151; border-bottom: 1px solid #e5e7eb;">ユニークID</th>
                            <th style="padding: 8px 10px; text-align: left; color: #374151; border-bottom: 1px solid #e5e7eb;">車体名</th>
                            <th style="padding: 8px 10px; text-align: right; color: #374151; border-bottom: 1px solid #e5e7eb;">価格</th>
                            <th style="padding: 8px 10px; text-align: center; color: #374151; border-bottom: 1px solid #e5e7eb;">画像</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($validatedRows as $row)
                        @php
                            $folder     = trim($row['画像フォルダ名'] ?? '');
                            $imageCount = isset($uploadedImageMap[$folder]) ? count($uploadedImageMap[$folder]) : 0;
                            $operation  = trim($row['操作'] ?? '');
                            $opColor    = match($operation) {
                                '新規' => '#15803d',
                                '更新' => '#1d4ed8',
                                '削除' => '#dc2626',
                                default => '#374151',
                            };
                            $opBg = match($operation) {
                                '新規' => '#f0fdf4',
                                '更新' => '#eff6ff',
                                '削除' => '#fef2f2',
                                default => '#f9fafb',
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 7px 10px;">
                                <span style="font-size: 11px; font-weight: 600; color: {{ $opColor }}; background: {{ $opBg }}; padding: 2px 6px; border-radius: 4px;">{{ $operation }}</span>
                            </td>
                            <td style="padding: 7px 10px; color: #374151; font-size: 11px;">{{ $row['ユニークID'] ?? '' }}</td>
                            <td style="padding: 7px 10px; color: #374151;">{{ $row['車体名'] ?? '' }}</td>
                            <td style="padding: 7px 10px; color: #374151; text-align: right;">¥{{ number_format((int)($row['支払価格（円）'] ?? 0)) }}</td>
                            <td style="padding: 7px 10px; text-align: center; color: {{ $imageCount > 0 ? '#15803d' : '#991b1b' }};">
                                {{ $operation === '削除' ? '-' : $imageCount . '枚' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button
                type="button"
                wire:click="importAndRequestApproval"
                wire:loading.attr="disabled"
                style="width: 100%; padding: 12px; background: #185FA5; color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer;"
            >
                <span wire:loading.remove>一括登録して承認依頼を送る</span>
                <span wire:loading>登録中...</span>
            </button>
        </div>
        @endif

        {{-- DONE: 完了 --}}
        @if($uploadStep === 'DONE')
        <div style="text-align: center; padding: 20px 0;">
            <p style="font-size: 36px; margin: 0 0 12px;">✅</p>
            <h2 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 6px;">承認依頼を送信しました</h2>
            <p style="font-size: 12px; color: #6b7280; margin: 0 0 16px;">
                {{ count($importedCarIds) }}台の車両を登録し、管理者に承認依頼を送りました。
            </p>
            <button
                type="button"
                wire:click="resetUpload"
                style="padding: 10px 20px; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; font-size: 13px; cursor: pointer;"
            >
                続けてアップロードする
            </button>
        </div>
        @endif

    </div>
</div>