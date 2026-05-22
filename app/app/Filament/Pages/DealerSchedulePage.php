<?php

namespace App\Filament\Pages;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Domain\DealerSchedule\Services\DealerScheduleService;
use App\Infrastructure\Eloquent\User\StkDealerSchedule;
use App\Infrastructure\Eloquent\User\StkDealerReservationTypes;
use App\Infrastructure\Eloquent\Opr\OprReservationTypes;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DealerSchedulePage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort  = NavigationSort::DEALER_SCHEDULE->value;
    protected static ?string $navigationLabel = 'スケジュール管理';
    protected static string  $view            = 'filament.pages.dealer-schedule';
    protected static ?string $title           = '';

    // -------------------------------------------------------
    // アクセス制御
    // -------------------------------------------------------

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->isDealerRole();
    }

    // -------------------------------------------------------
    // カレンダー表示用
    // -------------------------------------------------------

    public int   $displayYear;
    public int   $displayMonth;
    public array $calendarData = [];

    // -------------------------------------------------------
    // 一括生成フォーム
    // -------------------------------------------------------

    public bool   $showGenerateForm  = false;
    public string $generateDateFrom  = '';
    public string $generateDateTo    = '';

    /**
     * 生成フォームのSTEP
     * 'input'   → 条件入力中
     * 'preview' → プレビュー表示中
     */
    public string $generateStep    = 'input';
    public array  $previewNew      = []; // 新規追加分（最大20件）
    public int    $previewNewTotal  = 0;
    public array  $previewSkip     = []; // スキップ分（サンプル5件）
    public int    $previewSkipTotal = 0;

    /**
     * 曜日別スロット設定
     * weekdaySlots[曜日index] = [
     *   'is_holiday' => bool,
     *   'types' => [
     *     typeId => ['slots' => [['time_from'=>'', 'time_to'=>'', 'max_reservations'=>1], ...]]
     *   ]
     * ]
     * 曜日: 0=月 〜 6=日
     */
    public array  $weekdaySlots         = [];
    public array  $specificHolidays     = [];
    public string $specificHolidayInput = '';

    // -------------------------------------------------------
    // 曜日コピー
    // -------------------------------------------------------

    /** コピーパネルを開いている曜日index (nullなら閉じている) */
    public ?int  $copyingFromWeekday = null;

    /** コピー先曜日の選択状態 [0=>false, 1=>true, ...] */
    public array $copyTargetWeekdays = [];

    // -------------------------------------------------------
    // 日付クリックモーダル
    // -------------------------------------------------------

    public bool   $showDayModal      = false;
    public string $modalDate         = '';
    public bool   $modalIsPast       = false;
    public array  $modalSchedules    = [];
    public array  $deleteSelectedIds = [];
    public string $deleteReason      = '';
    public bool   $deleteAll         = false;

    // -------------------------------------------------------
    // 編集アコーディオン
    // -------------------------------------------------------

    public ?int   $editingScheduleId   = null;
    public string $editTimeFrom        = '';
    public string $editTimeTo          = '';
    public int    $editMaxReservations = 1;

    // -------------------------------------------------------
    // 時間帯追加フォーム（モーダル内）
    // -------------------------------------------------------

    public bool  $showAddSlotForm = false;
    public array $addSlotRows     = [];

    // -------------------------------------------------------
    // 初期化
    // -------------------------------------------------------

    public function mount(): void
    {
        $today              = now();
        $this->displayYear  = (int)$today->format('Y');
        $this->displayMonth = (int)$today->format('m');

        $this->initWeekdaySlots();
        $this->loadCalendar();
    }

    private function initWeekdaySlots(): void
    {
        for ($i = 0; $i <= 6; $i++) {
            $this->weekdaySlots[$i] = [
                'is_holiday' => false,
                'types'      => [],
            ];
        }
        $this->copyTargetWeekdays = array_fill(0, 7, false);
    }

    // -------------------------------------------------------
    // カレンダーナビゲーション
    // -------------------------------------------------------

    public function prevMonth(): void
    {
        if ($this->displayMonth === 1) {
            $this->displayMonth = 12;
            $this->displayYear--;
        } else {
            $this->displayMonth--;
        }
        $this->loadCalendar();
    }

    public function nextMonth(): void
    {
        if ($this->displayMonth === 12) {
            $this->displayMonth = 1;
            $this->displayYear++;
        } else {
            $this->displayMonth++;
        }
        $this->loadCalendar();
    }

    public function loadCalendar(): void
    {
        $dealerId = Auth::user()->dealer_id;
        $dateFrom = Carbon::create($this->displayYear, $this->displayMonth, 1)->format('Y-m-d');
        $dateTo   = Carbon::create($this->displayYear, $this->displayMonth, 1)->endOfMonth()->format('Y-m-d');

        $grouped = app(DealerScheduleService::class)
            ->getSchedulesGroupedByDate($dealerId, $dateFrom, $dateTo);

        $this->calendarData = $grouped->map(function (Collection $schedules) {
            $isClosed        = $schedules->contains('is_closed', true);
            $normalSchedules = $schedules->where('is_closed', false);

            $breakdown = $normalSchedules->groupBy('reservation_type_id')
                ->map(fn($group) => [
                    'type_name'  => $group->first()->reservationType?->name ?? '',
                    'slot_count' => $group->count(),
                    'max_total'  => $group->sum('max_reservations'),
                ])
                ->values()
                ->toArray();

            return [
                'schedules'  => $schedules->map(fn($s) => [
                    'id'                  => $s->id,
                    'reservation_type_id' => $s->reservation_type_id,
                    'type_name'           => $s->reservationType?->name ?? '',
                    'time_from'           => substr($s->time_from, 0, 5),
                    'time_to'             => substr($s->time_to,   0, 5),
                    'max_reservations'    => $s->max_reservations,
                    'is_available'        => $s->is_available,
                    'is_closed'           => $s->is_closed,
                    'active_count'        => $s->is_closed ? 0 : $s->active_reservation_count,
                    'is_full'             => $s->is_closed ? false : $s->is_full,
                ])->values()->toArray(),
                'slot_count' => $normalSchedules->count(),
                'max_total'  => $normalSchedules->sum('max_reservations'),
                'breakdown'  => $breakdown,
                'is_closed'  => $isClosed,
            ];
        })->toArray();
    }

    // -------------------------------------------------------
    // 一括生成フォーム
    // -------------------------------------------------------

    public function toggleGenerateForm(): void
    {
        $this->showGenerateForm = !$this->showGenerateForm;
        $this->generateStep     = 'input';
        $this->previewNew       = [];
        $this->previewNewTotal  = 0;
        $this->previewSkip      = [];
        $this->previewSkipTotal = 0;
    }

    /**
     * 開始日変更時：終了日が開始日より前になっていたらリセット
     */
    public function updatedGenerateDateFrom(string $value): void
    {
        if ($this->generateDateTo && $this->generateDateTo < $value) {
            $this->generateDateTo = '';
        }
    }

    /**
     * is_holiday チェック解除時：slotsが空なら各種別に1行自動追加
     */
    public function updatedWeekdaySlots(mixed $value, string $key): void
    {
        if (!str_ends_with($key, '.is_holiday')) return;

        if ($value == false) {
            $weekday = (int)explode('.', $key)[0];
            $types   = $this->weekdaySlots[$weekday]['types'] ?? [];

            if (empty($types)) {
                foreach ($this->getReservationTypes() as $type) {
                    $this->weekdaySlots[$weekday]['types'][$type['id']] = [
                        'slots' => [
                            ['time_from' => '', 'time_to' => '', 'max_reservations' => 1],
                        ],
                    ];
                }
            } else {
                foreach ($types as $typeId => $typeSetting) {
                    if (empty($typeSetting['slots'])) {
                        $this->weekdaySlots[$weekday]['types'][$typeId]['slots'][] = [
                            'time_from' => '', 'time_to' => '', 'max_reservations' => 1,
                        ];
                    }
                }
            }
        }
    }

    public function addSlotRow(int $weekday, int $typeId): void
    {
        $this->weekdaySlots[$weekday]['types'][$typeId]['slots'][] = [
            'time_from'        => '',
            'time_to'          => '',
            'max_reservations' => 1,
        ];
    }

    public function removeSlotRow(int $weekday, int $typeId, int $index): void
    {
        array_splice($this->weekdaySlots[$weekday]['types'][$typeId]['slots'], $index, 1);
    }

    public function addSpecificHoliday(): void
    {
        $date = trim($this->specificHolidayInput);
        if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            Notification::make()->title('日付形式が正しくありません（YYYY-MM-DD）')->warning()->send();
            return;
        }
        if (!in_array($date, $this->specificHolidays, true)) {
            $this->specificHolidays[] = $date;
        }
        $this->specificHolidayInput = '';
    }

    public function removeSpecificHoliday(int $index): void
    {
        array_splice($this->specificHolidays, $index, 1);
    }

    /**
     * プレビュー生成（STEP1 → STEP2）
     * DBへの書き込みは行わず新規／スキップを分類して表示
     */
    public function previewGenerate(): void
    {
        if (!$this->generateDateFrom || !$this->generateDateTo) {
            Notification::make()->title('開始日・終了日を入力してください')->warning()->send();
            return;
        }
        if ($this->generateDateFrom > $this->generateDateTo) {
            Notification::make()->title('開始日は終了日より前にしてください')->warning()->send();
            return;
        }

        $hasHolidayWeekday  = collect($this->weekdaySlots)->contains(fn($d) => $d['is_holiday']);
        $hasSpecificHoliday = !empty($this->specificHolidays);
        $hasType            = collect($this->weekdaySlots)->contains(function ($d) {
            if ($d['is_holiday']) return false;
            foreach ($d['types'] as $typeSetting) {
                if (!empty($typeSetting['slots'])) return true;
            }
            return false;
        });

        if (!$hasType && !$hasHolidayWeekday && !$hasSpecificHoliday) {
            Notification::make()->title('曜日別の時間帯、または定休日を少なくとも1つ設定してください')->warning()->send();
            return;
        }

        $dealerId     = Auth::user()->dealer_id;
        $existing     = app(DealerScheduleService::class)->getExistingKeysPublic($dealerId, $this->generateDateFrom, $this->generateDateTo);
        $serviceSlots = $this->buildServiceSlots();
        $newItems     = [];
        $skipItems    = [];
        $period       = \Carbon\CarbonPeriod::create($this->generateDateFrom, $this->generateDateTo);
        $dayNames     = ['月', '火', '水', '木', '金', '土', '日'];
        $typeNames    = collect($this->getReservationTypes())->pluck('name', 'id')->toArray();

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $weekday = $this->carbonDayToIndex($date);

            if (in_array($dateStr, $this->specificHolidays, true)) {
                $closedKey = "{$dateStr}_closed";
                isset($existing[$closedKey])
                    ? $skipItems[] = ['date' => $dateStr, 'day' => $dayNames[$weekday], 'label' => '定休日', 'reason' => '既存あり']
                    : $newItems[]  = ['date' => $dateStr, 'day' => $dayNames[$weekday], 'label' => '定休日'];
                continue;
            }

            $daySetting = $serviceSlots[$weekday] ?? null;
            if (!$daySetting) continue;

            if ($daySetting['is_holiday']) {
                $closedKey = "{$dateStr}_closed";
                isset($existing[$closedKey])
                    ? $skipItems[] = ['date' => $dateStr, 'day' => $dayNames[$weekday], 'label' => '定休日', 'reason' => '既存あり']
                    : $newItems[]  = ['date' => $dateStr, 'day' => $dayNames[$weekday], 'label' => '定休日'];
                continue;
            }

            foreach ($daySetting['type_slots'] as $slot) {
                $key      = "{$dateStr}_{$slot['type_id']}_{$slot['time_from']}_{$slot['time_to']}";
                $typeName = $typeNames[$slot['type_id']] ?? "種別{$slot['type_id']}";
                $label    = "{$typeName} {$slot['time_from']}〜{$slot['time_to']} 枠{$slot['max_reservations']}";

                isset($existing[$key])
                    ? $skipItems[] = ['date' => $dateStr, 'day' => $dayNames[$weekday], 'label' => "{$typeName} {$slot['time_from']}〜{$slot['time_to']}", 'reason' => '既存あり']
                    : $newItems[]  = ['date' => $dateStr, 'day' => $dayNames[$weekday], 'label' => $label];
            }
        }

        $this->previewNewTotal  = count($newItems);
        $this->previewSkipTotal = count($skipItems);
        $this->previewNew       = array_slice($newItems,  0, 20);
        $this->previewSkip      = array_slice($skipItems, 0, 5);
        $this->generateStep     = 'preview';
    }

    public function backToInput(): void
    {
        $this->generateStep = 'input';
    }

    /**
     * 一括生成実行（STEP2 → 完了）
     */
    public function generate(): void
    {
        $dealerId = Auth::user()->dealer_id;

        $result = app(DealerScheduleService::class)->bulkGenerateByTypeSlots(
            dealerId:         $dealerId,
            dateFrom:         $this->generateDateFrom,
            dateTo:           $this->generateDateTo,
            weekdaySlots:     $this->buildServiceSlots(),
            specificHolidays: $this->specificHolidays,
        );

        Notification::make()
            ->title("生成完了：{$result['created']}件作成")
            ->body($result['skipped'] > 0 ? "⚠️ {$result['skipped']}件は既存の設定と重複したためスキップしました。" : null)
            ->color($result['skipped'] > 0 ? 'warning' : 'success')
            ->send();

        $this->showGenerateForm = false;
        $this->generateStep     = 'input';
        $this->loadCalendar();
    }

    /**
     * weekdaySlots をService用フォーマットに変換
     */
    private function buildServiceSlots(): array
    {
        $serviceSlots = [];
        foreach ($this->weekdaySlots as $weekday => $daySetting) {
            $serviceSlots[$weekday] = [
                'is_holiday' => $daySetting['is_holiday'],
                'type_slots' => [],
            ];
            if (!$daySetting['is_holiday']) {
                foreach ($daySetting['types'] as $typeId => $typeSetting) {
                    foreach ($typeSetting['slots'] as $slot) {
                        if (empty($slot['time_from']) || empty($slot['time_to'])) continue;
                        $serviceSlots[$weekday]['type_slots'][] = [
                            'type_id'          => (int)$typeId,
                            'time_from'        => $slot['time_from'],
                            'time_to'          => $slot['time_to'],
                            'max_reservations' => (int)($slot['max_reservations'] ?? 1),
                        ];
                    }
                }
            }
        }
        return $serviceSlots;
    }

    /**
     * Carbonの曜日（0=日〜6=土）を 0=月〜6=日 に変換
     */
    private function carbonDayToIndex(\Carbon\Carbon $date): int
    {
        $day = $date->dayOfWeek;
        return $day === 0 ? 6 : $day - 1;
    }

    // -------------------------------------------------------
    // 曜日コピー
    // -------------------------------------------------------

    public function toggleCopyPanel(int $weekday): void
    {
        if ($this->copyingFromWeekday === $weekday) {
            $this->copyingFromWeekday = null;
        } else {
            $this->copyingFromWeekday             = $weekday;
            $this->copyTargetWeekdays             = array_fill(0, 7, false);
            $this->copyTargetWeekdays[$weekday]   = false;
        }
    }

    public function executeCopy(): void
    {
        if ($this->copyingFromWeekday === null) return;

        $from    = $this->copyingFromWeekday;
        $targets = array_keys(array_filter($this->copyTargetWeekdays));

        if (empty($targets)) {
            Notification::make()->title('コピー先の曜日を選択してください')->warning()->send();
            return;
        }

        $sourceSetting = $this->weekdaySlots[$from];
        foreach ($targets as $target) {
            $this->weekdaySlots[$target] = json_decode(json_encode($sourceSetting), true);
        }

        $dayNames    = $this->getWeekdayNames();
        $targetNames = implode('・', array_map(fn($t) => $dayNames[$t], $targets));

        Notification::make()
            ->title("{$dayNames[$from]}の設定を{$targetNames}にコピーしました")
            ->success()
            ->send();

        $this->copyingFromWeekday = null;
        $this->copyTargetWeekdays = array_fill(0, 7, false);
    }

    // -------------------------------------------------------
    // 日付クリック → モーダル
    // -------------------------------------------------------

    public function openDayModal(string $date): void
    {
        $this->modalDate           = $date;
        $this->modalIsPast         = $date < now()->toDateString();
        $this->deleteSelectedIds   = [];
        $this->deleteReason        = '';
        $this->deleteAll           = false;
        $this->showAddSlotForm     = false;
        $this->addSlotRows         = [];
        $this->editingScheduleId   = null;
        $this->editTimeFrom        = '';
        $this->editTimeTo          = '';
        $this->editMaxReservations = 1;

        $schedules = app(DealerScheduleService::class)
            ->getSchedulesByDate(Auth::user()->dealer_id, $date);

        $this->modalSchedules = $schedules->map(fn($s) => [
            'id'                  => $s->id,
            'reservation_type_id' => $s->reservation_type_id,
            'type_name'           => $s->reservationType?->name ?? '',
            'time_from'           => substr($s->time_from, 0, 5),
            'time_to'             => substr($s->time_to,   0, 5),
            'max_reservations'    => $s->max_reservations,
            'active_count'        => $s->active_reservation_count,
            'is_full'             => $s->is_full,
        ])->values()->toArray();

        $this->showDayModal = true;
    }

    public function closeDayModal(): void
    {
        $this->showDayModal = false;
    }

    public function updatedDeleteAll(bool $value): void
    {
        $this->deleteSelectedIds = $value
            ? array_column($this->modalSchedules, 'id')
            : [];
    }

    public function deleteSelected(): void
    {
        if ($this->modalIsPast) {
            Notification::make()->title('過去のスケジュールは削除できません')->warning()->send();
            return;
        }
        if (empty($this->deleteSelectedIds)) {
            Notification::make()->title('削除する時間帯を選択してください')->warning()->send();
            return;
        }
        if (empty(trim($this->deleteReason))) {
            Notification::make()->title('削除理由を入力してください')->warning()->send();
            return;
        }

        $hasReservation = collect($this->modalSchedules)
            ->whereIn('id', $this->deleteSelectedIds)
            ->where('active_count', '>', 0)
            ->isNotEmpty();

        if ($hasReservation) {
            Notification::make()
                ->title('予約が入っている時間帯は削除できません')
                ->body('先に予約をキャンセルしてから削除してください')
                ->danger()
                ->send();
            return;
        }

        $count = app(DealerScheduleService::class)
            ->softDeleteSchedules($this->deleteSelectedIds, $this->deleteReason);

        Notification::make()->title("{$count}件の時間帯を削除しました")->success()->send();

        $this->showDayModal = false;
        $this->loadCalendar();
    }

    // -------------------------------------------------------
    // 編集アコーディオン
    // -------------------------------------------------------

    public function toggleEdit(int $scheduleId): void
    {
        if ($this->editingScheduleId === $scheduleId) {
            $this->editingScheduleId = null;
            return;
        }

        $schedule = collect($this->modalSchedules)->firstWhere('id', $scheduleId);
        if (!$schedule) return;

        if ($this->modalIsPast) return;

        if ($schedule['active_count'] > 0) {
            Notification::make()
                ->title('予約が入っている時間帯は編集できません')
                ->body('先に予約をキャンセルしてから編集してください')
                ->warning()
                ->send();
            return;
        }

        $this->editingScheduleId   = $scheduleId;
        $this->editTimeFrom        = $schedule['time_from'];
        $this->editTimeTo          = $schedule['time_to'];
        $this->editMaxReservations = $schedule['max_reservations'];
    }

    public function closeEdit(): void
    {
        $this->editingScheduleId = null;
    }

    public function saveEdit(): void
    {
        if (!$this->editingScheduleId) return;

        if (!$this->editTimeFrom || !$this->editTimeTo) {
            Notification::make()->title('開始時間・終了時間を入力してください')->warning()->send();
            return;
        }
        if ($this->editTimeFrom >= $this->editTimeTo) {
            Notification::make()->title('開始時間は終了時間より前にしてください')->warning()->send();
            return;
        }

        StkDealerSchedule::where('id', $this->editingScheduleId)
            ->whereNull('deleted_at')
            ->update([
                'time_from'        => $this->editTimeFrom,
                'time_to'          => $this->editTimeTo,
                'max_reservations' => $this->editMaxReservations,
            ]);

        Notification::make()->title('時間帯を更新しました')->success()->send();

        $this->editingScheduleId = null;
        $this->openDayModal($this->modalDate);
        $this->loadCalendar();
    }

    // -------------------------------------------------------
    // 時間帯追加（モーダル内）
    // -------------------------------------------------------

    public function toggleAddSlotForm(): void
    {
        $this->showAddSlotForm = !$this->showAddSlotForm;
        $this->addSlotRows     = $this->showAddSlotForm
            ? [['type_id' => 0, 'time_from' => '', 'time_to' => '', 'max_reservations' => 1]]
            : [];
    }

    public function addSlotFormRow(): void
    {
        $this->addSlotRows[] = [
            'type_id'          => 0,
            'time_from'        => '',
            'time_to'          => '',
            'max_reservations' => 1,
        ];
    }

    public function removeSlotFormRow(int $index): void
    {
        array_splice($this->addSlotRows, $index, 1);
        if (empty($this->addSlotRows)) {
            $this->showAddSlotForm = false;
        }
    }

    public function saveAddSlot(): void
    {
        if ($this->modalIsPast) {
            Notification::make()->title('過去のスケジュールに時間帯を追加できません')->warning()->send();
            return;
        }
        if (empty($this->addSlotRows)) {
            Notification::make()->title('追加する時間帯がありません')->warning()->send();
            return;
        }

        foreach ($this->addSlotRows as $i => $row) {
            $no = $i + 1;
            if (empty($row['type_id'])) {
                Notification::make()->title("{$no}行目：予約種別を選択してください")->warning()->send();
                return;
            }
            if (empty($row['time_from']) || empty($row['time_to'])) {
                Notification::make()->title("{$no}行目：開始時間・終了時間を入力してください")->warning()->send();
                return;
            }
            if ($row['time_from'] >= $row['time_to']) {
                Notification::make()->title("{$no}行目：開始時間は終了時間より前にしてください")->warning()->send();
                return;
            }
        }

        $dealerId = Auth::user()->dealer_id;
        $saved    = 0;
        $skipped  = 0;

        foreach ($this->addSlotRows as $row) {
            $result = app(DealerScheduleService::class)->addSlot(
                dealerId:          $dealerId,
                date:              $this->modalDate,
                reservationTypeId: (int)$row['type_id'],
                timeFrom:          $row['time_from'],
                timeTo:            $row['time_to'],
                maxReservations:   (int)($row['max_reservations'] ?? 1),
            );
            $result === null ? $skipped++ : $saved++;
        }

        $msg = "{$saved}件の時間帯を追加しました";
        if ($skipped > 0) $msg .= "（{$skipped}件は重複のためスキップ）";

        Notification::make()->title($msg)->success()->send();

        $this->openDayModal($this->modalDate);
        $this->showAddSlotForm = false;
        $this->addSlotRows     = [];
        $this->loadCalendar();
    }

    // -------------------------------------------------------
    // View用ヘルパー
    // -------------------------------------------------------

    public function getReservationTypes(): array
    {
        $dealerId = Auth::user()->dealer_id;

        $typeIds = StkDealerReservationTypes::where('dealer_id', $dealerId)
            ->where('is_active', 1)
            ->pluck('reservation_type_id')
            ->toArray();

        if (empty($typeIds)) return [];

        return OprReservationTypes::whereIn('id', $typeIds)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get(['id', 'name'])
            ->toArray();
    }

    public function getCalendarCells(): array
    {
        $firstDay    = Carbon::create($this->displayYear, $this->displayMonth, 1);
        $totalDays   = $firstDay->daysInMonth;
        $today       = now()->format('Y-m-d');
        $startOffset = $firstDay->dayOfWeek === 0 ? 6 : $firstDay->dayOfWeek - 1;

        $cells = [];
        for ($i = 0; $i < $startOffset; $i++) {
            $cells[] = ['empty' => true];
        }

        for ($day = 1; $day <= $totalDays; $day++) {
            $dateStr     = Carbon::create($this->displayYear, $this->displayMonth, $day)->format('Y-m-d');
            $dayData     = $this->calendarData[$dateStr] ?? null;
            $hasSchedule = !empty($dayData);

            $cells[] = [
                'empty'       => false,
                'day'         => $day,
                'date'        => $dateStr,
                'isToday'     => $dateStr === $today,
                'isPast'      => $dateStr < $today,
                'hasSchedule' => $hasSchedule,
                'isClosed'    => $dayData['is_closed']  ?? false,
                'slotCount'   => $dayData['slot_count'] ?? 0,
                'maxTotal'    => $dayData['max_total']  ?? 0,
                'breakdown'   => $dayData['breakdown']  ?? [],
            ];
        }

        return $cells;
    }

    public function getWeekdayLabels(): array
    {
        return ['月', '火', '水', '木', '金', '土', '日'];
    }

    public function getWeekdayNames(): array
    {
        return ['月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日', '日曜日'];
    }

    private ?array $cachedTimeOptions = null;

    /**
     * 時間帯セレクトの選択肢（15分単位）
     * ディーラーの営業時間が設定されている場合はその範囲に絞る
     */
    public function getTimeOptions(): array
    {
        if ($this->cachedTimeOptions !== null) return $this->cachedTimeOptions;

        $dealerId = Auth::user()->dealer_id;
        $dealer   = \App\Infrastructure\Eloquent\User\StkCarDealer::find($dealerId);

        $fromStr = $dealer?->business_hours_from;
        $toStr   = $dealer?->business_hours_to;

        if ($fromStr && $toStr) {
            [$fromH, $fromM] = array_map('intval', explode(':', $fromStr));
            [$toH,   $toM  ] = array_map('intval', explode(':', $toStr));
            $fromTotal = $fromH * 60 + $fromM;
            $toTotal   = $toH   * 60 + $toM;
        } else {
            $fromTotal = 0;
            $toTotal   = 23 * 60 + 45;
        }

        $options = [];
        for ($h = 0; $h < 24; $h++) {
            foreach ([0, 15, 30, 45] as $m) {
                $total = $h * 60 + $m;
                if ($total < $fromTotal || $total > $toTotal) continue;
                $options[] = sprintf('%02d:%02d', $h, $m);
            }
        }

        return $this->cachedTimeOptions = $options;
    }
}