<x-filament-widgets::widget>
    <div class="drc-widget">
        <div class="drc-calendar-wrap">

            {{-- 月ナビゲーション --}}
            <div class="drc-nav">
                <button wire:click="prevMonth" class="drc-nav__btn">‹</button>
                <span class="drc-nav__label">
                    {{ $this->displayYear }}年{{ $this->displayMonth }}月
                </span>
                <button wire:click="nextMonth" class="drc-nav__btn">›</button>
            </div>

            {{-- カレンダーグリッド --}}
            <div class="drc-grid">
                @foreach ($this->getWeekdayLabels() as $label)
                <div class="drc-grid__header {{ $label === '土' ? 'is-sat' : '' }} {{ $label === '日' ? 'is-sun' : '' }}">
                    {{ $label }}
                </div>
                @endforeach

                @foreach ($this->getCalendarCells() as $cell)
                    @if ($cell['empty'])
                    <div class="drc-grid__cell is-empty"></div>
                    @else
                    <div
                        class="drc-grid__cell
                            {{ $cell['isToday']     ? 'is-today'        : '' }}
                            {{ $cell['isPast']      ? 'is-past'         : '' }}
                            {{ $cell['isClosed']    ? 'is-closed'       : '' }}
                            {{ $cell['totalCount'] > 0 ? 'is-has-reservation' : '' }}
                            {{ $cell['hasSchedule'] && !$cell['isClosed'] && $cell['totalCount'] === 0 ? 'is-has-schedule' : '' }}
                        "
                        @if($cell['totalCount'] > 0 || $cell['isClosed'])
                            wire:click="openModal('{{ $cell['date'] }}')"
                            style="cursor:pointer"
                        @endif
                    >
                        <span class="drc-grid__day">{{ $cell['day'] }}</span>

                        @if ($cell['isClosed'])
                            <span class="drc-grid__closed">定休日</span>
                        @elseif ($cell['totalCount'] > 0)
                            <span class="drc-grid__badge">合計 {{ $cell['totalCount'] }}件</span>
                            @foreach ($cell['byType'] as $type)
                            <span class="drc-grid__type">{{ $type['name'] }}：{{ $type['count'] }}件</span>
                            @endforeach
                        @endif
                    </div>
                    @endif
                @endforeach
            </div>

            {{-- 凡例 --}}
            <div class="drc-legend">
                <span class="drc-legend__item">
                    <span class="drc-legend__dot is-has-reservation"></span> 予約あり
                </span>
                <span class="drc-legend__item">
                    <span class="drc-legend__dot is-has-schedule"></span> 予約なし
                </span>
                <span class="drc-legend__item">
                    <span class="drc-legend__dot is-closed"></span> 定休日
                </span>
            </div>

            {{-- モーダル --}}
            @if ($this->showModal)
            <div class="drc-overlay" wire:click.self="closeModal">
                <div class="drc-modal">
                    <div class="drc-modal__header">
                        <h3 class="drc-modal__title">{{ $this->modalDate }} の予約</h3>
                        <button wire:click="closeModal" class="drc-modal__close">✕</button>
                    </div>
                    <div class="drc-modal__body">
                        @forelse ($this->modalReservations as $reservation)
                        <div class="drc-reservation">
                            {{-- メイン行 --}}
                            <div
                                class="drc-reservation__main"
                                wire:click="toggleExpand({{ $reservation['id'] }})"
                            >
                                <span class="drc-reservation__type">{{ $reservation['typeName'] }}</span>
                                <span class="drc-reservation__time">
                                    {{ $reservation['timeFrom'] }}〜{{ $reservation['timeTo'] }}
                                </span>
                                <span class="drc-reservation__guest">
                                    お客様：{{ $reservation['guestName'] }}
                                </span>
                                <span class="drc-reservation__status drc-reservation__status--{{ $reservation['statusColor'] }}">
                                    {{ $reservation['statusLabel'] }}
                                </span>
                                <span class="drc-reservation__toggle">
                                    {{ in_array($reservation['id'], $this->expandedIds) ? '▲' : '▽' }}
                                </span>
                            </div>

                            {{-- 展開部分 --}}
                            @if (in_array($reservation['id'], $this->expandedIds))
                            <div class="drc-reservation__detail">
                                <dl class="drc-detail-list">
                                    <div class="drc-detail-list__row">
                                        <dt>車両</dt>
                                        <dd>{{ $reservation['carName'] }}（在庫番号：{{ $reservation['stockNumber'] }}）</dd>
                                    </div>
                                    @if ($reservation['memo'])
                                    <div class="drc-detail-list__row">
                                        <dt>メモ</dt>
                                        <dd>{{ $reservation['memo'] }}</dd>
                                    </div>
                                    @endif
                                </dl>
                                
                                @php
                                    $now = now();
                                    $isToday = $reservation['date'] === $now->format('Y-m-d');
                                    $scheduleDateTime = \Carbon\Carbon::parse($reservation['date'] . ' ' . $reservation['timeFrom']);
                                    $isFuture = $scheduleDateTime->isFuture() || $isToday; // 当日は時間関係なく表示
                                @endphp

                                @if ($isFuture)
                                    @if ($isToday)
                                        <a href="{{ route('filament.admin.resources.today-reservations.view', ['record' => $reservation['id']]) }}" class="drc-reservation__link">
                                            当日対応はこちら →
                                        </a>
                                    @else
                                        <a href="{{ route('filament.admin.resources.reservations.view', ['record' => $reservation['id']]) }}" class="drc-reservation__link">
                                            詳細・対応はこちら →
                                        </a>
                                    @endif
                                @endif
                            </div>
                            @endif
                        </div>
                        @empty
                        <p class="drc-modal__empty">この日の予約はありません</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <style>
        .drc-widget { font-family: sans-serif; color: #e5e7eb; }

        /* ナビ */
        .drc-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .drc-nav__label { font-size: 1rem; font-weight: 600; color: #f9fafb; }
        .drc-nav__btn {
            width: 32px; height: 32px;
            border: 1px solid #374151;
            border-radius: 50%;
            background: transparent;
            color: #9ca3af;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        .drc-nav__btn:hover { border-color: #6366f1; color: #6366f1; }

        /* グリッド */
        .drc-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-bottom: 0.75rem;
        }
        .drc-grid__header {
            text-align: center;
            font-size: 0.75rem;
            color: #9ca3af;
            padding: 0.4rem 0;
        }
        .drc-grid__header.is-sat { color: #60a5fa; }
        .drc-grid__header.is-sun { color: #f87171; }

        .drc-grid__cell {
            min-height: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 3px;
            border: 1px solid #374151;
            border-radius: 6px;
            background: #111827;
            padding: 6px 4px;
            transition: all 0.15s;
        }
        .drc-grid__cell.is-empty        { border: none; background: transparent; }
        .drc-grid__cell.is-past         { opacity: 0.4; }
        .drc-grid__cell.is-today        { border-color: #6366f1; box-shadow: 0 0 0 1px #6366f1; }
        .drc-grid__cell.is-closed       { background: rgba(220,80,80,0.12); border-color: rgba(220,80,80,0.4); cursor: pointer; }
        .drc-grid__cell.is-has-schedule { background: #1e3a5f; border-color: #3b82f6; }
        .drc-grid__cell.is-has-reservation { background: #1e3a5f; border-color: #3b82f6; cursor: pointer; }
        .drc-grid__cell.is-has-reservation:hover { background: #1d4ed8; border-color: #6366f1; }

        .drc-grid__day {
            font-size: 0.8rem;
            font-weight: 500;
            color: #d1d5db;
        }
        .drc-grid__cell.is-today .drc-grid__day { color: #818cf8; font-weight: 700; }
        .drc-grid__closed {
            font-size: 0.6rem;
            color: #f87171;
            font-weight: 500;
        }
        .drc-grid__badge {
            font-size: 0.6rem;
            background: #3b82f6;
            color: #fff;
            border-radius: 999px;
            padding: 1px 6px;
            white-space: nowrap;
        }
        .drc-grid__type {
            font-size: 0.55rem;
            color: #9ca3af;
            white-space: nowrap;
        }

        /* 凡例 */
        .drc-legend { display: flex; gap: 1rem; margin-top: 0.5rem; }
        .drc-legend__item { display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: #9ca3af; }
        .drc-legend__dot { width: 12px; height: 12px; border-radius: 3px; border: 1px solid #374151; display: inline-block; background: #111827; }
        .drc-legend__dot.is-has-reservation { background: #1e3a5f; border-color: #3b82f6; }
        .drc-legend__dot.is-has-schedule    { background: #1e3a5f; border-color: #3b82f6; opacity: 0.5; }
        .drc-legend__dot.is-closed          { background: rgba(220,80,80,0.12); border-color: rgba(220,80,80,0.4); }

        /* モーダル */
        .drc-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
        .drc-modal {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 10px;
            width: 90%;
            max-width: 560px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
        }
        .drc-modal__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #374151;
        }
        .drc-modal__title { font-size: 1rem; font-weight: 600; color: #f9fafb; }
        .drc-modal__close { background: none; border: none; color: #9ca3af; font-size: 1.1rem; cursor: pointer; }
        .drc-modal__close:hover { color: #ef4444; }
        .drc-modal__body { padding: 1.25rem; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 0.75rem; }
        .drc-modal__empty { font-size: 0.875rem; color: #9ca3af; }

        /* 予約アイテム */
        .drc-reservation {
            background: #111827;
            border: 1px solid #374151;
            border-radius: 6px;
            overflow: hidden;
        }
        .drc-reservation__main {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            cursor: pointer;
            flex-wrap: wrap;
        }
        .drc-reservation__main:hover { background: #1f2937; }
        .drc-reservation__type   { font-size: 0.8rem; font-weight: 600; color: #818cf8; flex-shrink: 0; }
        .drc-reservation__time   { font-size: 0.875rem; color: #e5e7eb; flex-shrink: 0; }
        .drc-reservation__guest  { font-size: 0.8rem; color: #d1d5db; flex: 1; }
        .drc-reservation__toggle { font-size: 0.7rem; color: #6b7280; margin-left: auto; flex-shrink: 0; }

        .drc-reservation__status {
            font-size: 0.7rem;
            border-radius: 4px;
            padding: 2px 8px;
            flex-shrink: 0;
        }
        .drc-reservation__status--success { background: rgba(34,197,94,0.15); color: #4ade80; }
        .drc-reservation__status--warning { background: rgba(251,191,36,0.15); color: #fbbf24; }
        .drc-reservation__status--danger  { background: rgba(239,68,68,0.15);  color: #f87171; }
        .drc-reservation__status--gray    { background: rgba(156,163,175,0.15); color: #9ca3af; }

        .drc-reservation__detail {
            padding: 0.75rem 1rem;
            border-top: 1px solid #374151;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .drc-detail-list { display: flex; flex-direction: column; gap: 0.4rem; margin: 0; padding: 0; }
        .drc-detail-list__row { display: flex; gap: 0.75rem; font-size: 0.8rem; }
        .drc-detail-list__row dt { color: #9ca3af; min-width: 40px; flex-shrink: 0; }
        .drc-detail-list__row dd { color: #d1d5db; margin: 0; }

        .drc-reservation__link {
            display: inline-block;
            font-size: 0.8rem;
            color: #818cf8;
            text-decoration: none;
            padding: 0.4rem 0.75rem;
            border: 1px solid #4f46e5;
            border-radius: 4px;
            transition: background 0.15s;
            align-self: flex-start;
        }
        .drc-reservation__link:hover { background: rgba(99,102,241,0.1); }

        .drc-calendar-wrap {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 10px;
            padding: 1.25rem;
        }
    </style>
</x-filament-widgets::widget>