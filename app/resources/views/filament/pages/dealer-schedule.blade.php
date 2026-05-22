<x-filament-panels::page>
    <div class="dealer-schedule">

        {{-- ===== ページ注意書き ===== --}}
        <div class="ds-page-notice">
            <div class="ds-page-notice__icon">ℹ️</div>
            <div class="ds-page-notice__body">
                <div class="ds-page-notice__title">このページについて</div>
                <ul class="ds-page-notice__list">
                    <li>このページはサイト上でお客様が予約できる<strong>日時枠の管理</strong>を行うページです。</li>
                    <li>店舗の定休日・営業時間などの<strong>店舗情報の変更は「店舗情報」ページ</strong>よりご対応ください。こちらでの変更はサイト上の予約枠にのみ反映され、店舗情報ページの定休日表示には反映されません。</li>
                    <li>定休日の設定を誤ると、サイト上では予約不可でも実際は営業日となり、お客様との<strong>トラブルにつながる恐れがあります</strong>。設定の際は店舗情報と照らし合わせてご確認ください。</li>
                </ul>
            </div>
        </div>

        {{-- ヘッダー --}}
        <div class="ds-header">
            <h2 class="ds-header__title">スケジュール管理</h2>
            <button
                wire:click="toggleGenerateForm"
                class="ds-btn ds-btn--primary"
            >
                {{ $this->showGenerateForm ? 'キャンセル' : '＋ スケジュールを一括生成' }}
            </button>
        </div>

        {{-- ===== 一括生成フォーム ===== --}}
        @if ($this->showGenerateForm)
        <div class="ds-form-panel">
            <h3 class="ds-form-panel__title">
                スケジュール一括生成
                @if ($this->generateStep === 'preview')
                <span style="font-size:0.75rem;color:#9ca3af;font-weight:400;margin-left:0.5rem;">
                    {{ $this->generateDateFrom }} 〜 {{ $this->generateDateTo }}
                </span>
                @endif
            </h3>

            {{-- 注意書き --}}
            <div class="ds-notice">
                <div class="ds-notice__icon">⚠️</div>
                <ul class="ds-notice__list">
                    <li>生成する期間にすでにスケジュールが登録されている場合があります。生成前にカレンダーでご確認ください。</li>
                    <li>既存のスケジュールと重複する時間帯はスキップされます。意図せず重複している場合は「← 条件に戻る」からキャンセルしてください。</li>
                    <li>特定の日時のみ追加・変更したい場合は、カレンダーの日付をクリックすると時間帯の追加・編集が可能です。</li>
                </ul>
            </div>

            @if ($this->generateStep === 'input')

            {{-- 期間設定 --}}
            <div class="ds-section">
                <h4 class="ds-section__title">① 対象期間</h4>
                <div class="ds-row">
                    <div class="ds-field">
                        <label class="ds-label">開始日</label>
                        <input
                            type="date"
                            wire:model.live="generateDateFrom"
                            class="ds-input"
                        >
                    </div>
                    <span class="ds-row__sep">〜</span>
                    <div class="ds-field">
                        <label class="ds-label">終了日</label>
                        <input
                            type="date"
                            wire:model="generateDateTo"
                            class="ds-input"
                            min="{{ $this->generateDateFrom ?: '' }}"
                        >
                    </div>
                </div>
            </div>

            {{-- 曜日別時間帯設定 --}}
            <div class="ds-section">
                <h4 class="ds-section__title">② 曜日別時間帯設定</h4>
                <div class="ds-weekday-list">
                    @foreach ($this->getWeekdayNames() as $i => $dayName)
                    <div class="ds-weekday-row">
                        {{-- 曜日ヘッダー --}}
                        <div class="ds-weekday-row__head">
                            <span class="ds-weekday-row__name">{{ $dayName }}</span>
                            <label class="ds-checkbox-label ds-checkbox-label--holiday">
                                <input
                                    type="checkbox"
                                    wire:model.live="weekdaySlots.{{ $i }}.is_holiday"
                                    class="ds-checkbox"
                                >
                                定休日
                            </label>
                            @if (!($weekdaySlots[$i]['is_holiday'] ?? false))
                            <button
                                wire:click="toggleCopyPanel({{ $i }})"
                                class="ds-btn ds-btn--copy"
                                type="button"
                            >
                                {{ $this->copyingFromWeekday === $i ? 'コピーキャンセル' : '他の曜日にコピー' }}
                            </button>
                            @endif
                        </div>

                        {{-- コピーパネル --}}
                        @if ($this->copyingFromWeekday === $i)
                        <div class="ds-copy-panel">
                            <span class="ds-copy-panel__label">コピー先：</span>
                            @foreach ($this->getWeekdayNames() as $ti => $tName)
                            @if ($ti !== $i)
                            <label class="ds-checkbox-label">
                                <input
                                    type="checkbox"
                                    wire:model.live="copyTargetWeekdays.{{ $ti }}"
                                    class="ds-checkbox"
                                >
                                {{ $tName }}
                            </label>
                            @endif
                            @endforeach
                            <button
                                wire:click="executeCopy"
                                class="ds-btn ds-btn--primary"
                                type="button"
                            >コピー実行</button>
                        </div>
                        @endif

                        {{-- 種別別スロット設定 --}}
                        @if (!($weekdaySlots[$i]['is_holiday'] ?? false))
                        <div class="ds-weekday-row__types">
                            @foreach ($this->getReservationTypes() as $type)
                            <div class="ds-type-block">
                                <div class="ds-type-block__title">{{ $type['name'] }}</div>
                                <div class="ds-weekday-row__slots">
                                    @foreach ($weekdaySlots[$i]['types'][$type['id']]['slots'] ?? [] as $si => $slot)
                                    <div class="ds-slot-row">
                                        <select
                                            wire:model="weekdaySlots.{{ $i }}.types.{{ $type['id'] }}.slots.{{ $si }}.time_from"
                                            style="background:#111827;border:1px solid #374151;border-radius:6px;color:#e5e7eb;padding:0.4rem 0.6rem;font-size:0.875rem;width:90px;appearance:auto!important;-webkit-appearance:auto!important;background-image:none!important;"
                                        >
                                            <option value="">開始</option>
                                            @foreach ($this->getTimeOptions() as $t)
                                            <option value="{{ $t }}">{{ $t }}</option>
                                            @endforeach
                                        </select>
                                        <span class="ds-slot-row__sep">〜</span>
                                        <select
                                            wire:model="weekdaySlots.{{ $i }}.types.{{ $type['id'] }}.slots.{{ $si }}.time_to"
                                            style="background:#111827;border:1px solid #374151;border-radius:6px;color:#e5e7eb;padding:0.4rem 0.6rem;font-size:0.875rem;width:90px;appearance:auto!important;-webkit-appearance:auto!important;background-image:none!important;"
                                        >
                                            <option value="">終了</option>
                                            @foreach ($this->getTimeOptions() as $t)
                                            <option value="{{ $t }}">{{ $t }}</option>
                                            @endforeach
                                        </select>
                                        <div class="ds-field ds-field--inline">
                                            <label class="ds-label ds-label--sm">最大枠数</label>
                                            <input
                                                type="number"
                                                wire:model="weekdaySlots.{{ $i }}.types.{{ $type['id'] }}.slots.{{ $si }}.max_reservations"
                                                min="1"
                                                max="99"
                                                class="ds-input ds-input--sm"
                                            >
                                        </div>
                                        <button
                                            wire:click="removeSlotRow({{ $i }}, {{ $type['id'] }}, {{ $si }})"
                                            class="ds-btn ds-btn--danger-sm"
                                            type="button"
                                        >✕</button>
                                    </div>
                                    @endforeach
                                    <button
                                        wire:click="addSlotRow({{ $i }}, {{ $type['id'] }})"
                                        class="ds-btn ds-btn--add-slot"
                                        type="button"
                                    >＋ {{ $type['name'] }}の時間帯を追加</button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="ds-weekday-row__holiday-label">定休日</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- 特定日除外 --}}
            <div class="ds-section">
                <h4 class="ds-section__title">③ 特定日除外（任意）</h4>
                <div class="ds-row">
                    <input
                        type="date"
                        wire:model="specificHolidayInput"
                        class="ds-input"
                    >
                    <button
                        wire:click="addSpecificHoliday"
                        class="ds-btn ds-btn--secondary"
                        type="button"
                    >追加</button>
                </div>
                @if (!empty($specificHolidays))
                <div class="ds-tag-list">
                    @foreach ($specificHolidays as $hi => $holiday)
                    <span class="ds-tag">
                        {{ $holiday }}
                        <button
                            wire:click="removeSpecificHoliday({{ $hi }})"
                            class="ds-tag__remove"
                            type="button"
                        >✕</button>
                    </span>
                    @endforeach
                </div>
                @endif
            </div>
            @endif {{-- generateStep === 'input' --}}

            {{-- 生成ボタン / プレビュー --}}
            @if ($this->generateStep === 'input')
            <div class="ds-form-panel__footer">
                <button
                    wire:click="previewGenerate"
                    wire:loading.attr="disabled"
                    class="ds-btn ds-btn--primary ds-btn--lg"
                    type="button"
                >
                    <span wire:loading.remove wire:target="previewGenerate">内容を確認する →</span>
                    <span wire:loading wire:target="previewGenerate">確認中...</span>
                </button>
            </div>

            @else
            {{-- ===== プレビュー ===== --}}
            <div class="ds-preview">
                <h4 class="ds-preview__title">生成内容の確認</h4>

                {{-- 新規追加 --}}
                <div class="ds-preview-section">
                    <div class="ds-preview-section__head is-new">
                        ● 新規追加
                        <span class="ds-preview-section__count">{{ $this->previewNewTotal }}件</span>
                    </div>
                    @if (!empty($this->previewNew))
                    <div class="ds-preview-list">
                        @foreach ($this->previewNew as $item)
                        <div class="ds-preview-item is-new">
                            <span class="ds-preview-item__date">{{ $item['date'] }}（{{ $item['day'] }}）</span>
                            <span class="ds-preview-item__label">{{ $item['label'] }}</span>
                        </div>
                        @endforeach
                        @if ($this->previewNewTotal > 20)
                        <div class="ds-preview-more">他 {{ $this->previewNewTotal - 20 }} 件</div>
                        @endif
                    </div>
                    @else
                    <div class="ds-preview-empty">新規追加はありません</div>
                    @endif
                </div>

                {{-- スキップ --}}
                @if ($this->previewSkipTotal > 0)
                <div class="ds-preview-section">
                    <div class="ds-preview-section__head is-skip">
                        ⚠️ スキップ（既存あり）
                        <span class="ds-preview-section__count">{{ $this->previewSkipTotal }}件</span>
                    </div>
                    <div class="ds-preview-list">
                        @foreach ($this->previewSkip as $item)
                        <div class="ds-preview-item is-skip">
                            <span class="ds-preview-item__date">{{ $item['date'] }}（{{ $item['day'] }}）</span>
                            <span class="ds-preview-item__label">{{ $item['label'] }}</span>
                        </div>
                        @endforeach
                        @if ($this->previewSkipTotal > 5)
                        <div class="ds-preview-more">他 {{ $this->previewSkipTotal - 5 }} 件（既存設定があるためスキップ）</div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="ds-preview__actions">
                    <button
                        wire:click="backToInput"
                        class="ds-btn ds-btn--secondary"
                        type="button"
                    >← 条件に戻る</button>
                    <button
                        wire:click="generate"
                        wire:loading.attr="disabled"
                        class="ds-btn ds-btn--primary ds-btn--lg"
                        type="button"
                    >
                        <span wire:loading.remove wire:target="generate">
                            {{ $this->previewNewTotal }}件を生成する
                        </span>
                        <span wire:loading wire:target="generate">生成中...</span>
                    </button>
                </div>
            </div>
            @endif

        </div>
        @endif

        {{-- ===== カレンダー ===== --}}
        <div class="ds-calendar">

            {{-- 月ナビゲーション --}}
            <div class="ds-calendar__nav">
                <button wire:click="prevMonth" class="ds-calendar__nav-btn">‹</button>
                <span class="ds-calendar__nav-label">
                    {{ $this->displayYear }}年{{ $this->displayMonth }}月
                </span>
                <button wire:click="nextMonth" class="ds-calendar__nav-btn">›</button>
            </div>

            {{-- 曜日ヘッダー --}}
            <div class="ds-calendar__grid">
                @foreach ($this->getWeekdayLabels() as $label)
                <div class="ds-calendar__header-cell
                    {{ $label === '土' ? 'is-sat' : '' }}
                    {{ $label === '日' ? 'is-sun' : '' }}
                ">
                    {{ $label }}
                </div>
                @endforeach

                {{-- 日付セル --}}
                @foreach ($this->getCalendarCells() as $cell)
                    @if ($cell['empty'])
                    <div class="ds-calendar__cell is-empty"></div>
                    @else
                    <div
                        class="ds-calendar__cell
                            {{ $cell['isToday']     ? 'is-today'        : '' }}
                            {{ $cell['isPast']      ? 'is-past'         : '' }}
                            {{ $cell['isClosed']    ? 'is-closed'       : '' }}
                            {{ $cell['hasSchedule'] && !$cell['isClosed'] ? 'is-has-schedule' : '' }}
                        "
                        @if($cell['hasSchedule'] || $cell['isClosed'])
                            wire:click="openDayModal('{{ $cell['date'] }}')"
                            style="cursor:pointer"
                        @endif
                    >
                        <span class="ds-calendar__cell-day">{{ $cell['day'] }}</span>
                        @if ($cell['isClosed'])
                        <span class="ds-calendar__cell-closed">定休日</span>
                        @elseif ($cell['hasSchedule'])
                        <span class="ds-calendar__cell-badge">
                            {{ $cell['slotCount'] }}枠 / {{ $cell['maxTotal'] }}件
                        </span>
                        @foreach ($cell['breakdown'] as $b)
                        <span class="ds-calendar__cell-breakdown">
                            {{ $b['type_name'] }} {{ $b['max_total'] }}件
                        </span>
                        @endforeach
                        @endif
                    </div>
                    @endif
                @endforeach
            </div>

            {{-- 凡例 --}}
            <div class="ds-calendar__legend">
                <span class="ds-legend-item">
                    <span class="ds-legend-dot is-has-schedule"></span> 設定済み
                </span>
                <span class="ds-legend-item">
                    <span class="ds-legend-dot is-closed"></span> 定休日
                </span>
                <span class="ds-legend-item">
                    <span class="ds-legend-dot"></span> 未設定
                </span>
            </div>
        </div>

        {{-- ===== 日付クリックモーダル ===== --}}
        @if ($this->showDayModal)
        <div class="ds-modal-overlay" wire:click.self="closeDayModal">
            <div class="ds-modal">

                <div class="ds-modal__header">
                    <h3 class="ds-modal__title">
                        {{ $this->modalDate }} のスケジュール
                        @if ($this->modalIsPast)
                        <span class="ds-modal__past-badge">過去・閲覧のみ</span>
                        @endif
                    </h3>
                    <button wire:click="closeDayModal" class="ds-modal__close">✕</button>
                </div>

                <div class="ds-modal__body">

                    {{-- スケジュール一覧 --}}
                    @if (!empty($modalSchedules))
                    <div class="ds-modal-section">
                        <div class="ds-modal-section__header">
                            <h4 class="ds-modal-section__title">設定済み時間帯</h4>
                            @if (!$this->modalIsPast)
                            <label class="ds-checkbox-label">
                                <input
                                    type="checkbox"
                                    wire:model.live="deleteAll"
                                    class="ds-checkbox"
                                >
                                すべて選択
                            </label>
                            @endif
                        </div>

                        <div class="ds-slot-list">
                            @foreach ($modalSchedules as $schedule)
                            <div class="ds-slot-item {{ $schedule['is_full'] ? 'is-full' : '' }} {{ $this->editingScheduleId === $schedule['id'] ? 'is-editing' : '' }}">
                                {{-- メイン行 --}}
                                <div class="ds-slot-item__main">
                                    @if (!$this->modalIsPast)
                                    <label class="ds-slot-item__check">
                                        <input
                                            type="checkbox"
                                            wire:model.live="deleteSelectedIds"
                                            value="{{ $schedule['id'] }}"
                                            class="ds-checkbox"
                                            {{ $schedule['active_count'] > 0 ? 'disabled' : '' }}
                                        >
                                    </label>
                                    @endif
                                    <div
                                        class="ds-slot-item__info {{ $schedule['active_count'] > 0 ? 'is-locked' : '' }}"
                                        wire:click="{{ $schedule['active_count'] > 0 ? '' : 'toggleEdit(' . $schedule['id'] . ')' }}"
                                        style="flex:1; cursor:{{ $schedule['active_count'] > 0 ? 'not-allowed' : 'pointer' }};"
                                    >
                                        <span class="ds-slot-item__type">{{ $schedule['type_name'] }}</span>
                                        <span class="ds-slot-item__time">
                                            {{ $schedule['time_from'] }} 〜 {{ $schedule['time_to'] }}
                                        </span>
                                        <span class="ds-slot-item__count">
                                            最大{{ $schedule['max_reservations'] }}枠
                                            @if ($schedule['active_count'] > 0)
                                            <span class="ds-slot-item__reserved">
                                                （予約{{ $schedule['active_count'] }}件）
                                            </span>
                                            @endif
                                        </span>
                                        <span class="ds-slot-item__edit-hint">
                                            @if ($schedule['active_count'] === 0)
                                            {{ $this->editingScheduleId === $schedule['id'] ? '▲' : '▼' }}
                                            @endif
                                        </span>
                                    </div>
                                    @if ($schedule['active_count'] > 0)
                                    <span class="ds-slot-item__locked">予約あり</span>
                                    @endif
                                </div>

                                {{-- アコーディオン編集フォーム --}}
                                @if ($this->editingScheduleId === $schedule['id'])
                                <div class="ds-slot-edit-form">
                                    <div class="ds-slot-edit-form__row">
                                        <div class="ds-field">
                                            <label class="ds-label">開始時間</label>
                                            <select
                                                wire:model="editTimeFrom"
                                                style="background:#111827;border:1px solid #374151;border-radius:6px;color:#e5e7eb;padding:0.4rem 0.6rem;font-size:0.875rem;width:90px;appearance:auto!important;-webkit-appearance:auto!important;background-image:none!important;"
                                            >
                                                <option value="">選択</option>
                                                @foreach ($this->getTimeOptions() as $t)
                                                <option value="{{ $t }}">{{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="ds-row__sep">〜</span>
                                        <div class="ds-field">
                                            <label class="ds-label">終了時間</label>
                                            <select
                                                wire:model="editTimeTo"
                                                style="background:#111827;border:1px solid #374151;border-radius:6px;color:#e5e7eb;padding:0.4rem 0.6rem;font-size:0.875rem;width:90px;appearance:auto!important;-webkit-appearance:auto!important;background-image:none!important;"
                                            >
                                                <option value="">選択</option>
                                                @foreach ($this->getTimeOptions() as $t)
                                                <option value="{{ $t }}">{{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="ds-field">
                                            <label class="ds-label">最大枠数</label>
                                            <input
                                                type="number"
                                                wire:model="editMaxReservations"
                                                min="1"
                                                max="99"
                                                style="background:#111827;border:1px solid #374151;border-radius:6px;color:#e5e7eb;padding:0.4rem 0.6rem;font-size:0.875rem;width:60px;"
                                            >
                                        </div>
                                    </div>
                                    <div class="ds-slot-edit-form__actions">
                                        <button
                                            wire:click="saveEdit"
                                            class="ds-btn ds-btn--primary"
                                            type="button"
                                        >保存</button>
                                        <button
                                            wire:click="closeEdit"
                                            class="ds-btn ds-btn--secondary"
                                            type="button"
                                        >閉じる</button>
                                    </div>
                                </div>
                                @endif

                            </div>
                            @endforeach
                        </div>

                        {{-- 削除理由・削除ボタン（過去日は非表示） --}}
                        @if (!$this->modalIsPast && !empty($this->deleteSelectedIds))
                        <div class="ds-delete-section">
                            <label class="ds-label">削除理由 <span class="ds-required">必須</span></label>
                            <input
                                type="text"
                                wire:model="deleteReason"
                                placeholder="例：スタッフ不足、店舗都合など"
                                class="ds-input ds-input--full"
                            >
                            <button
                                wire:click="deleteSelected"
                                class="ds-btn ds-btn--danger"
                                type="button"
                            >
                                選択した時間帯を削除（{{ count($this->deleteSelectedIds) }}件）
                            </button>
                        </div>
                        @endif
                    </div>
                    @else
                    <p class="ds-modal__empty">この日のスケジュールはありません</p>
                    @endif

                    {{-- 時間帯追加フォーム（過去日は非表示） --}}
                    @if (!$this->modalIsPast)
                    <div class="ds-modal-section">
                        <button
                            wire:click="toggleAddSlotForm"
                            class="ds-btn ds-btn--add-slot"
                            type="button"
                        >
                            {{ $this->showAddSlotForm ? 'キャンセル' : '＋ 時間帯を追加' }}
                        </button>

                        @if ($this->showAddSlotForm)
                        <div class="ds-add-slot-form">

                            {{-- 追加行一覧 --}}
                            @foreach ($this->addSlotRows as $ri => $row)
                            <div class="ds-add-slot-row">
                                <div class="ds-add-slot-row__fields">
                                    {{-- 予約種別 --}}
                                    <div class="ds-field">
                                        <label class="ds-label">予約種別</label>
                                        <select
                                            wire:model="addSlotRows.{{ $ri }}.type_id"
                                            style="background:#111827;border:1px solid #374151;border-radius:6px;color:#e5e7eb;padding:0.4rem 0.6rem;font-size:0.875rem;width:200px;appearance:auto!important;-webkit-appearance:auto!important;background-image:none!important;"
                                        >
                                            <option value="0">選択してください</option>
                                            @foreach ($this->getReservationTypes() as $type)
                                            <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- 時間帯 --}}
                                    <div class="ds-row ds-row--wrap">
                                        <div class="ds-field">
                                            <label class="ds-label">開始時間</label>
                                            <select
                                                wire:model="addSlotRows.{{ $ri }}.time_from"
                                                style="background:#111827;border:1px solid #374151;border-radius:6px;color:#e5e7eb;padding:0.4rem 0.6rem;font-size:0.875rem;width:90px;appearance:auto!important;-webkit-appearance:auto!important;background-image:none!important;"
                                            >
                                                <option value="">選択</option>
                                                @foreach ($this->getTimeOptions() as $t)
                                                <option value="{{ $t }}">{{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="ds-row__sep">〜</span>
                                        <div class="ds-field">
                                            <label class="ds-label">終了時間</label>
                                            <select
                                                wire:model="addSlotRows.{{ $ri }}.time_to"
                                                style="background:#111827;border:1px solid #374151;border-radius:6px;color:#e5e7eb;padding:0.4rem 0.6rem;font-size:0.875rem;width:90px;appearance:auto!important;-webkit-appearance:auto!important;background-image:none!important;"
                                            >
                                                <option value="">選択</option>
                                                @foreach ($this->getTimeOptions() as $t)
                                                <option value="{{ $t }}">{{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="ds-field">
                                            <label class="ds-label">最大枠数</label>
                                            <input
                                                type="number"
                                                wire:model="addSlotRows.{{ $ri }}.max_reservations"
                                                min="1"
                                                max="99"
                                                class="ds-input ds-input--sm"
                                            >
                                        </div>
                                    </div>
                                </div>

                                {{-- 行削除ボタン --}}
                                @if (count($this->addSlotRows) > 1)
                                <button
                                    wire:click="removeSlotFormRow({{ $ri }})"
                                    class="ds-btn ds-btn--danger-sm ds-add-slot-row__remove"
                                    type="button"
                                >✕</button>
                                @endif
                            </div>
                            @endforeach

                            {{-- 行追加ボタン --}}
                            <button
                                wire:click="addSlotFormRow"
                                class="ds-btn ds-btn--add-slot"
                                type="button"
                            >＋ 行を追加</button>

                            {{-- 保存ボタン --}}
                            <button
                                wire:click="saveAddSlot"
                                class="ds-btn ds-btn--primary"
                                type="button"
                            >保存（{{ count($this->addSlotRows) }}件）</button>
                        </div>
                        @endif
                    </div>
                    @endif {{-- /modalIsPast --}}

                </div>{{-- /.ds-modal__body --}}
            </div>{{-- /.ds-modal --}}
        </div>{{-- /.ds-modal-overlay --}}
        @endif

    </div>{{-- /.dealer-schedule --}}

    <style>
        /* ===== ベース ===== */
        .dealer-schedule {
            font-family: sans-serif;
            color: #e5e7eb;
        }

        /* ===== ページ注意書き ===== */
        .ds-page-notice {
            display: flex;
            gap: 0.875rem;
            background: rgba(99, 102, 241, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-left: 4px solid #6366f1;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }
        .ds-page-notice__icon {
            font-size: 1.1rem;
            flex-shrink: 0;
            padding-top: 0.1rem;
        }
        .ds-page-notice__body {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .ds-page-notice__title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #a5b4fc;
        }
        .ds-page-notice__list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .ds-page-notice__list li {
            font-size: 0.8rem;
            color: #d1d5db;
            line-height: 1.7;
            padding-left: 0.875rem;
            position: relative;
        }
        .ds-page-notice__list li::before {
            content: '・';
            position: absolute;
            left: 0;
            color: #818cf8;
        }
        .ds-page-notice__list strong {
            color: #fbbf24;
            font-weight: 600;
        }

        /* ===== ヘッダー ===== */
        .ds-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .ds-header__title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #f9fafb;
        }

        /* ===== ボタン ===== */
        .ds-btn {
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: background 0.15s;
        }
        .ds-btn--primary     { background: #6366f1; color: #fff; }
        .ds-btn--primary:hover { background: #4f46e5; }
        .ds-btn--secondary   { background: #374151; color: #e5e7eb; }
        .ds-btn--secondary:hover { background: #4b5563; }
        .ds-btn--danger      { background: #ef4444; color: #fff; margin-top: 0.75rem; }
        .ds-btn--danger:hover { background: #dc2626; }
        .ds-btn--danger-sm   { background: #ef4444; color: #fff; padding: 0.25rem 0.5rem; font-size: 0.75rem; border-radius: 4px; cursor: pointer; border: none; }
        .ds-btn--add-slot    { background: #1f2937; color: #9ca3af; border: 1px dashed #4b5563; padding: 0.4rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.8rem; }
        .ds-btn--add-slot:hover { background: #374151; color: #e5e7eb; }
        .ds-btn--lg          { padding: 0.75rem 2rem; font-size: 1rem; }
        .ds-btn--copy {
            background: #1f2937;
            color: #818cf8;
            border: 1px solid #4f46e5;
            padding: 0.3rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            cursor: pointer;
            margin-left: auto;
        }
        .ds-btn--copy:hover { background: #312e81; }
        .ds-btn:disabled     { opacity: 0.5; cursor: not-allowed; }

        /* ===== フォームパネル ===== */
        .ds-form-panel {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .ds-form-panel__title {
            font-size: 1rem;
            font-weight: 600;
            color: #f9fafb;
            margin-bottom: 1.25rem;
        }
        .ds-form-panel__footer {
            margin-top: 1.5rem;
            display: flex;
            justify-content: flex-end;
        }

        /* ===== セクション ===== */
        .ds-section {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #374151;
        }
        .ds-section:last-child { border-bottom: none; }
        .ds-section__title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #9ca3af;
            margin-bottom: 0.75rem;
        }

        /* ===== 共通フォームパーツ ===== */
        .ds-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .ds-row__sep  { color: #9ca3af; font-size: 0.875rem; }
        .ds-row--wrap { flex-wrap: wrap; }
        .ds-field { display: flex; flex-direction: column; gap: 0.25rem; }
        .ds-field--inline { flex-direction: row; align-items: center; gap: 0.5rem; }
        .ds-label     { font-size: 0.75rem; color: #9ca3af; }
        .ds-label--sm { font-size: 0.75rem; white-space: nowrap; }
        .ds-required  { color: #ef4444; font-size: 0.7rem; margin-left: 4px; }
        .ds-input {
            background: #111827;
            border: 1px solid #374151;
            border-radius: 6px;
            color: #e5e7eb;
            padding: 0.4rem 0.6rem;
            font-size: 0.875rem;
        }
        .ds-input--sm   { width: 60px; }
        .ds-input--full { width: 100%; }

        /* ===== チェックボックス ===== */
        .ds-checkbox-group { display: flex; flex-wrap: wrap; gap: 0.75rem; }
        .ds-checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.875rem;
            color: #e5e7eb;
            cursor: pointer;
        }
        .ds-checkbox-label--holiday { color: #f87171; }
        .ds-checkbox { width: 16px; height: 16px; cursor: pointer; accent-color: #6366f1; }

        /* ===== 曜日別スロット ===== */
        .ds-weekday-list { display: flex; flex-direction: column; gap: 0.75rem; }
        .ds-weekday-row {
            background: #111827;
            border: 1px solid #374151;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        .ds-weekday-row__head {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }
        .ds-weekday-row__name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #f9fafb;
            min-width: 48px;
        }
        .ds-weekday-row__types {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }
        .ds-weekday-row__slots { display: flex; flex-direction: column; gap: 0.5rem; }
        .ds-weekday-row__holiday-label {
            font-size: 0.8rem;
            color: #f87171;
            padding: 0.25rem 0;
        }
        .ds-type-block {
            background: #0d1117;
            border: 1px solid #374151;
            border-radius: 6px;
            padding: 0.6rem 0.75rem;
        }
        .ds-type-block__title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #818cf8;
            margin-bottom: 0.5rem;
        }
        .ds-slot-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* ===== コピーパネル ===== */
        .ds-copy-panel {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            background: #111827;
            border: 1px solid #4f46e5;
            border-radius: 6px;
            padding: 0.75rem;
            margin-top: 0.5rem;
        }
        .ds-copy-panel__label {
            font-size: 0.8rem;
            color: #9ca3af;
            white-space: nowrap;
        }

        /* ===== 特定日タグ ===== */
        .ds-tag-list { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem; }
        .ds-tag {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            background: #374151;
            border-radius: 999px;
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
            color: #e5e7eb;
        }
        .ds-tag__remove {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 0.7rem;
            padding: 0;
            line-height: 1;
        }
        .ds-tag__remove:hover { color: #ef4444; }

        /* ===== 注意書き（フォーム内） ===== */
        .ds-notice {
            display: flex;
            gap: 0.75rem;
            background: rgba(251, 191, 36, 0.08);
            border: 1px solid rgba(251, 191, 36, 0.3);
            border-radius: 8px;
            padding: 0.875rem 1rem;
            margin-bottom: 1.25rem;
        }
        .ds-notice__icon { font-size: 1rem; flex-shrink: 0; padding-top: 0.1rem; }
        .ds-notice__list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .ds-notice__list li {
            font-size: 0.78rem;
            color: #d1d5db;
            line-height: 1.6;
            padding-left: 0.75rem;
            position: relative;
        }
        .ds-notice__list li::before {
            content: '・';
            position: absolute;
            left: 0;
            color: #fbbf24;
        }

        /* ===== プレビュー ===== */
        .ds-preview {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .ds-preview__title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #f9fafb;
            margin-bottom: 0.25rem;
        }
        .ds-preview-section {
            background: #111827;
            border: 1px solid #374151;
            border-radius: 8px;
            overflow: hidden;
        }
        .ds-preview-section__head {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 0.875rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .ds-preview-section__head.is-new  { background: rgba(59,130,246,0.15); color: #60a5fa; }
        .ds-preview-section__head.is-skip { background: rgba(251,191,36,0.12); color: #fbbf24; }
        .ds-preview-section__count { margin-left: auto; font-size: 0.8rem; font-weight: 700; }
        .ds-preview-list {
            padding: 0.5rem 0;
            max-height: 260px;
            overflow-y: auto;
        }
        .ds-preview-item {
            display: flex;
            gap: 0.75rem;
            padding: 0.3rem 0.875rem;
            font-size: 0.8rem;
            border-bottom: 1px solid #1f2937;
        }
        .ds-preview-item:last-child { border-bottom: none; }
        .ds-preview-item.is-new  { color: #d1d5db; }
        .ds-preview-item.is-skip { color: #9ca3af; }
        .ds-preview-item__date { flex-shrink: 0; color: #9ca3af; min-width: 120px; }
        .ds-preview-item__label { flex: 1; }
        .ds-preview-more {
            padding: 0.4rem 0.875rem;
            font-size: 0.75rem;
            color: #6b7280;
            font-style: italic;
        }
        .ds-preview-empty {
            padding: 0.75rem 0.875rem;
            font-size: 0.8rem;
            color: #6b7280;
        }
        .ds-preview__actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
        }

        /* ===== カレンダー ===== */
        .ds-calendar {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 10px;
            padding: 1.25rem;
        }
        .ds-calendar__nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .ds-calendar__nav-label {
            font-size: 1rem;
            font-weight: 600;
            color: #f9fafb;
        }
        .ds-calendar__nav-btn {
            width: 32px; height: 32px;
            border: 1px solid #374151;
            border-radius: 50%;
            background: transparent;
            color: #9ca3af;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.15s;
        }
        .ds-calendar__nav-btn:hover { border-color: #6366f1; color: #6366f1; }
        .ds-calendar__grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-bottom: 0.75rem;
        }
        .ds-calendar__header-cell {
            text-align: center;
            font-size: 0.75rem;
            color: #9ca3af;
            padding: 0.4rem 0;
        }
        .ds-calendar__header-cell.is-sat { color: #60a5fa; }
        .ds-calendar__header-cell.is-sun { color: #f87171; }
        .ds-calendar__cell {
            min-height: 72px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            border: 1px solid #374151;
            border-radius: 6px;
            background: #111827;
            padding: 4px;
            transition: all 0.15s;
        }
        .ds-calendar__cell.is-empty   { border: none; background: transparent; }
        .ds-calendar__cell.is-past    { opacity: 0.35; }
        .ds-calendar__cell.is-today   { border-color: #6366f1; }
        .ds-calendar__cell.is-closed  {
            background: rgba(220, 80, 80, 0.12);
            border-color: rgba(220, 80, 80, 0.4);
            cursor: pointer;
        }
        .ds-calendar__cell.is-closed:hover {
            background: rgba(220, 80, 80, 0.2);
            border-color: rgba(220, 80, 80, 0.6);
        }
        .ds-calendar__cell.is-has-schedule {
            background: #1e3a5f;
            border-color: #3b82f6;
            cursor: pointer;
        }
        .ds-calendar__cell.is-has-schedule:hover {
            background: #1d4ed8;
            border-color: #6366f1;
        }
        .ds-calendar__cell-day {
            font-size: 0.8rem;
            font-weight: 500;
            color: #d1d5db;
        }
        .ds-calendar__cell.is-today .ds-calendar__cell-day { color: #818cf8; font-weight: 700; }
        .ds-calendar__cell-closed {
            font-size: 0.6rem;
            color: #f87171;
            font-weight: 500;
            letter-spacing: 0.03em;
        }
        .ds-calendar__cell-badge {
            font-size: 0.6rem;
            background: #3b82f6;
            color: #fff;
            border-radius: 999px;
            padding: 1px 5px;
            white-space: nowrap;
        }
        .ds-calendar__cell-breakdown {
            font-size: 0.55rem;
            color: #9ca3af;
            white-space: nowrap;
            line-height: 1.3;
        }
        .ds-calendar__legend {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        .ds-legend-item {
            display: flex; align-items: center; gap: 0.4rem;
            font-size: 0.75rem; color: #9ca3af;
        }
        .ds-legend-dot {
            width: 12px; height: 12px;
            border-radius: 3px;
            background: #111827;
            border: 1px solid #374151;
            display: inline-block;
        }
        .ds-legend-dot.is-has-schedule { background: #1e3a5f; border-color: #3b82f6; }
        .ds-legend-dot.is-closed       { background: rgba(220,80,80,0.12); border-color: rgba(220,80,80,0.4); }

        /* ===== モーダル ===== */
        .ds-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
        .ds-modal {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 10px;
            width: 90%;
            max-width: 560px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
        }
        .ds-modal__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #374151;
            flex-shrink: 0;
        }
        .ds-modal__title { font-size: 1rem; font-weight: 600; color: #f9fafb; }
        .ds-modal__past-badge {
            font-size: 0.7rem;
            background: #374151;
            color: #9ca3af;
            border-radius: 4px;
            padding: 2px 8px;
            margin-left: 0.5rem;
            font-weight: 400;
            vertical-align: middle;
        }
        .ds-modal__close {
            background: none;
            border: none;
            color: #9ca3af;
            font-size: 1.1rem;
            cursor: pointer;
            line-height: 1;
        }
        .ds-modal__close:hover { color: #ef4444; }
        .ds-modal__body {
            padding: 1.25rem;
            overflow-y: auto;
            flex: 1;
        }
        .ds-modal__empty { font-size: 0.875rem; color: #9ca3af; }

        /* ===== モーダル内セクション ===== */
        .ds-modal-section {
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #374151;
        }
        .ds-modal-section:last-child { border-bottom: none; }
        .ds-modal-section__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        .ds-modal-section__title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #9ca3af;
        }

        /* ===== スロット一覧（モーダル内） ===== */
        .ds-slot-list { display: flex; flex-direction: column; gap: 0.5rem; }
        .ds-slot-item {
            display: flex;
            flex-direction: column;
            background: #111827;
            border: 1px solid #374151;
            border-radius: 6px;
            padding: 0.6rem 0.75rem;
            transition: border-color 0.15s;
        }
        .ds-slot-item.is-full    { border-color: #dc2626; }
        .ds-slot-item.is-editing { border-color: #6366f1; }
        .ds-slot-item__main {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
        }
        .ds-slot-item__check { flex-shrink: 0; }
        .ds-slot-item__info {
            display: flex;
            flex: 1;
            flex-wrap: wrap;
            gap: 0.4rem 0.75rem;
            align-items: center;
        }
        .ds-slot-item__info.is-locked { opacity: 0.6; }
        .ds-slot-item__type     { font-size: 0.8rem; font-weight: 500; color: #818cf8; }
        .ds-slot-item__time     { font-size: 0.875rem; color: #e5e7eb; }
        .ds-slot-item__count    { font-size: 0.75rem; color: #9ca3af; }
        .ds-slot-item__reserved { color: #f87171; }
        .ds-slot-item__locked {
            font-size: 0.7rem;
            background: #7f1d1d;
            color: #fca5a5;
            border-radius: 4px;
            padding: 2px 6px;
            flex-shrink: 0;
        }
        .ds-slot-item__edit-hint {
            font-size: 0.7rem;
            color: #6b7280;
            margin-left: auto;
            flex-shrink: 0;
        }

        /* ===== アコーディオン編集フォーム ===== */
        .ds-slot-edit-form {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #374151;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .ds-slot-edit-form__row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .ds-slot-edit-form__actions { display: flex; gap: 0.5rem; }

        /* ===== 削除セクション ===== */
        .ds-delete-section {
            margin-top: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        /* ===== 時間帯追加フォーム ===== */
        .ds-add-slot-form {
            margin-top: 0.75rem;
            background: #111827;
            border: 1px solid #374151;
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .ds-add-slot-row {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 6px;
            padding: 0.75rem;
        }
        .ds-add-slot-row__fields {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .ds-add-slot-row__remove {
            flex-shrink: 0;
            margin-top: 0.25rem;
        }
    </style>
</x-filament-panels::page>