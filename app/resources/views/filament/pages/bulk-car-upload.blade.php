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
                files.forEach(f => console.log(f.name, f.size, f.webkitRelativePath));
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

                        console.log('チャンク送信', formData.length, '枚');
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

    {{-- ===== ステップインジケーター ===== --}}
    <div style="display: flex; align-items: center; gap: 0; margin-bottom: 24px;">
        @php
            $steps = [
                'STEP1' => ['label' => 'STEP 1', 'sub' => 'xlsx アップロード'],
                'STEP2' => ['label' => 'STEP 2', 'sub' => '画像アップロード'],
                'STEP3' => ['label' => 'STEP 3', 'sub' => '確認・承認依頼'],
                'DONE'  => ['label' => '完了',   'sub' => '承認依頼送信済み'],
            ];
            $stepOrder  = ['STEP1', 'STEP2', 'STEP3', 'DONE'];
            $currentIdx = array_search($step, $stepOrder);
        @endphp
        @foreach($steps as $key => $s)
            @php
                $idx       = array_search($key, $stepOrder);
                $isDone    = $idx < $currentIdx;
                $isCurrent = $key === $step;
                $bg        = $isDone ? '#185FA5' : ($isCurrent ? '#2E75B6' : '#e5e7eb');
                $color     = ($isDone || $isCurrent) ? '#fff' : '#6b7280';
            @endphp
            <div style="display: flex; align-items: center; flex: 1;">
                <div style="flex: 1; text-align: center; padding: 10px 8px; background: {{ $bg }}; border-radius: 6px;">
                    <p style="margin: 0; font-size: 11px; font-weight: 700; color: {{ $color }};">{{ $s['label'] }}</p>
                    <p style="margin: 2px 0 0; font-size: 10px; color: {{ $color }}; opacity: 0.85;">{{ $s['sub'] }}</p>
                </div>
                @if(!$loop->last)
                    <div style="width: 24px; height: 2px; background: {{ $isDone ? '#185FA5' : '#e5e7eb' }};"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ===== STEP1: xlsx アップロード ===== --}}
    @if($step === 'STEP1')
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
        <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 32px; max-width: 640px; margin: 0 auto;">
            <h2 style="font-size: 15px; font-weight: 600; color: #111827; margin: 0 0 8px;">xlsxファイルをアップロード</h2>
            <p style="font-size: 12px; color: #6b7280; margin: 0 0 24px;">
                車両一括登録フォーマット（.xlsx）を選択してください。<br>
                フォーマットは <a href="#" style="color: #2E75B6;">こちら</a> からダウンロードできます。
            </p>
            <div
                @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false"
                @drop.prevent="dragging = false; handleFile($event.dataTransfer.files[0])"
                @click="$refs.fileInput.click()"
                :style="dragging ? 'border: 2px dashed #2E75B6; border-radius: 10px; padding: 40px 24px; text-align: center; cursor: pointer; background: #EFF6FF;' : 'border: 2px dashed #d1d5db; border-radius: 10px; padding: 40px 24px; text-align: center; cursor: pointer;'"
            >
                <input type="file" x-ref="fileInput" accept=".xlsx" style="display: none;"
                    @change="handleFile($event.target.files[0])">
                <template x-if="!uploading && !filename">
                    <div>
                        <p style="font-size: 32px; margin: 0 0 8px;">📂</p>
                        <p style="font-size: 13px; color: #374151; margin: 0 0 4px;">クリックまたはドラッグ＆ドロップ</p>
                        <p style="font-size: 11px; color: #9ca3af; margin: 0;">.xlsx形式のみ対応</p>
                    </div>
                </template>
                <template x-if="uploading">
                    <div>
                        <p style="font-size: 13px; color: #2E75B6; margin: 0;">バリデーション中...</p>
                    </div>
                </template>
                <template x-if="!uploading && filename">
                    <div>
                        <p style="font-size: 13px; color: #374151; margin: 0;">📄 <span x-text="filename"></span></p>
                    </div>
                </template>
            </div>
        </div>

        @if(!empty($xlsxErrors))
        <div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 20px; margin-top: 20px; max-width: 640px; margin-left: auto; margin-right: auto;">
            <p style="font-size: 13px; font-weight: 600; color: #991b1b; margin: 0 0 12px;">
                ⚠️ バリデーションエラー（{{ count($xlsxErrors) }}件）
            </p>
            <div style="max-height: 320px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead>
                        <tr style="background: #fee2e2;">
                            <th style="padding: 6px 10px; text-align: left; color: #991b1b; border-bottom: 1px solid #fca5a5;">行</th>
                            <th style="padding: 6px 10px; text-align: left; color: #991b1b; border-bottom: 1px solid #fca5a5;">列</th>
                            <th style="padding: 6px 10px; text-align: left; color: #991b1b; border-bottom: 1px solid #fca5a5;">エラー内容</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($xlsxErrors as $error)
                        <tr style="border-bottom: 1px solid #fee2e2;">
                            <td style="padding: 6px 10px; color: #7f1d1d;">{{ $error['row'] }}</td>
                            <td style="padding: 6px 10px; color: #7f1d1d;">{{ $error['column'] }}</td>
                            <td style="padding: 6px 10px; color: #7f1d1d;">{{ $error['message'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p style="font-size: 11px; color: #991b1b; margin: 12px 0 0;">スプレッドシートを修正して再度アップロードしてください。</p>
        </div>
        @endif
    </div>
    @endif

    {{-- ===== STEP2: 画像アップロード ===== --}}
    @if($step === 'STEP2')
    <div x-data="imageUploader">
        <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 32px; max-width: 640px; margin: 0 auto;">
            <h2 style="font-size: 15px; font-weight: 600; color: #111827; margin: 0 0 8px;">画像をアップロード</h2>

            <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;">
                <p style="font-size: 12px; color: #0369a1; margin: 0;">
                    登録予定車両：<strong>{{ $previewSummary['total'] }}台</strong>　
                    必要フォルダ数：<strong>{{ $previewSummary['folders'] }}個</strong>
                </p>
            </div>

            <p style="font-size: 12px; color: #6b7280; margin: 0 0 16px;">
                フォルダ構造：<code style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 11px;">bulkCar/car1/001.jpg</code><br>
                親フォルダ（bulkCar）を選択してください。001が自動的にメイン画像になります。
            </p>

            <div
                style="border: 2px dashed #d1d5db; border-radius: 10px; padding: 32px 24px; text-align: center; cursor: pointer; margin-bottom: 16px;"
                @click="$refs.imgInput.click()"
            >
                <input
                    type="file"
                    x-ref="imgInput"
                    accept="image/*"
                    multiple
                    webkitdirectory
                    mozdirectory
                    style="display: none;"
                    @change="handleFiles($event)"
                >
                <p style="font-size: 32px; margin: 0 0 8px;">📁</p>
                <p style="font-size: 13px; color: #374151; margin: 0 0 4px;">クリックしてフォルダを選択</p>
                <p style="font-size: 11px; color: #9ca3af; margin: 0;">親フォルダ（bulkCar）を選択してください</p>
            </div>

            <template x-if="fileCount > 0">
                <p style="font-size: 12px; color: #374151; margin: 0 0 16px;">
                    選択済み：<strong x-text="fileCount + '枚'"></strong>
                </p>
            </template>

            <template x-if="uploading">
                <div style="margin-bottom: 16px;">
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
                        ? 'flex: 1; padding: 12px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: not-allowed; opacity: 0.5;'
                        : 'flex: 1; padding: 12px; background: #185FA5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;'"
                >
                    <span x-show="!uploading">アップロードして確認する</span>
                    <span x-show="uploading">アップロード中...</span>
                </button>
                <button
                    type="button"
                    wire:click="resetImageUpload"
                    style="padding: 12px 16px; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; font-size: 13px; cursor: pointer;"
                >
                    やり直す
                </button>
            </div>
        </div>

        @if(!empty($imageErrors))
        <div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 20px; margin-top: 20px; max-width: 640px; margin-left: auto; margin-right: auto;">
            <p style="font-size: 13px; font-weight: 600; color: #991b1b; margin: 0 0 12px;">
                ⚠️ 画像エラー（{{ count($imageErrors) }}件）
            </p>
            @foreach($imageErrors as $error)
            <div style="background: white; border-radius: 6px; padding: 10px 14px; margin-bottom: 8px; border-left: 3px solid #E24B4A;">
                <p style="font-size: 12px; color: #7f1d1d; margin: 0;">📁 {{ $error['folder'] }}：{{ $error['message'] }}</p>
            </div>
            @endforeach
            <p style="font-size: 11px; color: #991b1b; margin: 8px 0 0;">フォルダ構造を確認して再度アップロードしてください。</p>
        </div>
        @endif
    </div>
    @endif

    {{-- ===== STEP3: 確認・承認依頼 ===== --}}
    @if($step === 'STEP3')
    <div style="max-width: 640px; margin: 0 auto;">
        <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 32px; margin-bottom: 20px;">
            <h2 style="font-size: 15px; font-weight: 600; color: #111827; margin: 0 0 16px;">登録内容の確認</h2>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">
                <div style="background: #f0f9ff; border-radius: 8px; padding: 16px; text-align: center;">
                    <p style="font-size: 28px; font-weight: 700; color: #185FA5; margin: 0;">{{ $previewSummary['total'] }}</p>
                    <p style="font-size: 12px; color: #0369a1; margin: 4px 0 0;">登録予定台数</p>
                </div>
                <div style="background: #f0fdf4; border-radius: 8px; padding: 16px; text-align: center;">
                    <p style="font-size: 28px; font-weight: 700; color: #15803d; margin: 0;">
                        {{ collect($uploadedImageMap)->sum(fn($paths) => count($paths)) }}
                    </p>
                    <p style="font-size: 12px; color: #15803d; margin: 4px 0 0;">アップロード済み画像</p>
                </div>
            </div>

            <div style="max-height: 300px; overflow-y: auto; margin-bottom: 24px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead style="position: sticky; top: 0; background: #f9fafb;">
                        <tr>
                            <th style="padding: 8px 10px; text-align: left; color: #374151; border-bottom: 1px solid #e5e7eb;">#</th>
                            <th style="padding: 8px 10px; text-align: left; color: #374151; border-bottom: 1px solid #e5e7eb;">操作</th>
                            <th style="padding: 8px 10px; text-align: left; color: #374151; border-bottom: 1px solid #e5e7eb;">ユニークID</th>
                            <th style="padding: 8px 10px; text-align: left; color: #374151; border-bottom: 1px solid #e5e7eb;">車体名</th>
                            <th style="padding: 8px 10px; text-align: left; color: #374151; border-bottom: 1px solid #e5e7eb;">価格</th>
                            <th style="padding: 8px 10px; text-align: center; color: #374151; border-bottom: 1px solid #e5e7eb;">画像</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($validatedRows as $idx => $row)
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
                            <td style="padding: 7px 10px; color: #6b7280;">{{ $idx + 1 }}</td>
                            <td style="padding: 7px 10px;">
                                <span style="font-size: 11px; font-weight: 600; color: {{ $opColor }}; background: {{ $opBg }}; padding: 2px 8px; border-radius: 4px;">
                                    {{ $operation }}
                                </span>
                            </td>
                            <td style="padding: 7px 10px; color: #374151; font-size: 11px;">{{ $row['ユニークID'] ?? '' }}</td>
                            <td style="padding: 7px 10px; color: #374151;">{{ $row['車体名'] ?? '' }}</td>
                            <td style="padding: 7px 10px; color: #374151;">¥{{ number_format((int)($row['支払価格（円）'] ?? 0)) }}</td>
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
                style="width: 100%; padding: 14px; background: #185FA5; color: white; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;"
            >
                <span wire:loading.remove>一括登録して承認依頼を送る</span>
                <span wire:loading>登録中...</span>
            </button>
        </div>
    </div>
    @endif

    {{-- ===== DONE: 完了 ===== --}}
    @if($step === 'DONE')
    <div style="max-width: 480px; margin: 40px auto; text-align: center;">
        <p style="font-size: 48px; margin: 0 0 16px;">✅</p>
        <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 8px;">承認依頼を送信しました</h2>
        <p style="font-size: 13px; color: #6b7280; margin: 0 0 24px;">
            {{ count($importedCarIds) }}台の車両を登録し、管理者に承認依頼を送りました。<br>
            承認されるまでしばらくお待ちください。
        </p>
        <a
            href="{{ \App\Filament\Resources\CarRegistrationResource::getUrl('index') }}"
            style="display: inline-block; padding: 12px 24px; background: #185FA5; color: white; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;"
        >
            車両一覧に戻る
        </a>
    </div>
    @endif

</x-filament-panels::page>