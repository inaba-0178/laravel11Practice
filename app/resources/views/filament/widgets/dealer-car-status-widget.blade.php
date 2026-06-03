<x-filament-widgets::widget>
    <div class="dcs-widget">
        <div class="dcs-wrap">

            {{-- タイトル --}}
            <h3 class="dcs-title">車両登録承認状況</h3>

            {{-- サマリー --}}
            @php $summary = $this->getSummary(); @endphp
            <div class="dcs-summary__grid">
                <div class="dcs-summary__item">
                    <span class="dcs-summary__label">承認待ち</span>
                    <span class="dcs-summary__value dcs-summary__value--warning">{{ $summary['pending'] }}件</span>
                </div>
                <div class="dcs-summary__item">
                    <span class="dcs-summary__label">公開中</span>
                    <span class="dcs-summary__value dcs-summary__value--success">{{ $summary['available'] }}件</span>
                </div>
                <div class="dcs-summary__item">
                    <span class="dcs-summary__label">差し戻し</span>
                    <span class="dcs-summary__value dcs-summary__value--danger">{{ $summary['rejected'] }}件</span>
                </div>
            </div>

            {{-- 一覧テーブル --}}
            @php $cars = $this->getCars(); @endphp
            <div class="dcs-table-section">
                @if(empty($cars))
                    <p class="dcs-empty">現在なし</p>
                @else
                <div class="dcs-scroll-wrap">
                    <div class="dcs-scroll">
                        <table class="dcs-table">
                            <thead>
                                <tr>
                                    <th>返却日時</th>
                                    <th>ステータス</th>
                                    <th>登録日時</th>
                                    <th>車両名</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cars as $car)
                                @php
                                    $isRejected  = $car['status'] === 'rejected';
                                    $isAvailable = $car['status'] === 'available';
                                    $rowClass    = $isRejected ? 'is-rejected' : ($isAvailable ? 'is-approved' : '');
                                    $statusClass = $isRejected ? 'is-danger' : ($isAvailable ? 'is-success' : 'is-warning');
                                @endphp
                                <tr
                                    class="dcs-table__row {{ $rowClass }}"
                                    onclick="window.location.href='{{ $car['detailUrl'] }}'"
                                    style="cursor: pointer;"
                                >
                                    <td class="dcs-table__td">{{ $car['updatedAt'] ?? '---' }}</td>
                                    <td class="dcs-table__td">
                                        <span class="dcs-badge dcs-badge--{{ $statusClass }}">
                                            {{ $car['statusLabel'] }}
                                        </span>
                                    </td>
                                    <td class="dcs-table__td">{{ $car['createdAt'] }}</td>
                                    <td class="dcs-table__td">{{ $car['seriesName'] }}</td>
                                    <td class="dcs-table__td">
                                        <a href="{{ $car['detailUrl'] }}" class="dcs-link">詳細</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>

    <style>
        .dcs-widget { font-family: sans-serif; color: #e5e7eb; }

        .dcs-wrap {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 10px;
            overflow: hidden;
        }

        .dcs-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #f9fafb;
            margin: 0;
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid #374151;
        }

        /* サマリー */
        .dcs-summary__grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            background: #1f2937;
            border-bottom: 1px solid #374151;
            padding: 0.75rem 1rem;
        }
        .dcs-summary__item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            background: #111827;
            padding: 0.75rem 1rem;
            border-radius: 6px;
        }
        .dcs-summary__label {
            font-size: 0.7rem;
            color: #9ca3af;
        }
        .dcs-summary__value {
            font-size: 1rem;
            font-weight: 600;
            color: #f9fafb;
        }
        .dcs-summary__value--warning { color: #fbbf24; }
        .dcs-summary__value--success { color: #4ade80; }
        .dcs-summary__value--danger  { color: #f87171; }

        /* テーブルセクション */
        .dcs-table-section {
            background: #1f2937;
            padding: 0.75rem 1rem;
        }
        .dcs-scroll-wrap {
            border: 1px solid #374151;
            border-radius: 8px;
            overflow: hidden;
        }
        .dcs-scroll {
            overflow-y: auto;
            max-height: calc(10 * 41px);
        }
        .dcs-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.7rem;
        }
        .dcs-table thead th {
            padding: 0.5rem 0.75rem;
            text-align: left;
            color: #9ca3af;
            background: #111827;
            border-bottom: 1px solid #374151;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .dcs-table__row {
            border-bottom: 1px solid #1a1a2e;
            transition: background 0.1s;
            cursor: pointer;
        }
        .dcs-table__row:hover       { background: #1f2937; }
        .dcs-table__row.is-rejected { background: rgba(239,68,68,0.08); }
        .dcs-table__row.is-approved { background: rgba(34,197,94,0.08); }
        .dcs-table__td {
            padding: 0.5rem 0.75rem;
            color: #d1d5db;
            white-space: nowrap;
        }

        .dcs-badge {
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 500;
        }
        .dcs-badge--is-warning { background: rgba(251,191,36,0.15); color: #fbbf24; }
        .dcs-badge--is-success { background: rgba(34,197,94,0.15);  color: #4ade80; }
        .dcs-badge--is-danger  { background: rgba(239,68,68,0.15);  color: #f87171; }

        .dcs-link {
            font-size: 0.7rem;
            color: #818cf8;
            text-decoration: none;
            border: 1px solid #4f46e5;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .dcs-link:hover { background: rgba(99,102,241,0.1); }

        .dcs-empty {
            padding: 2rem;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
        }
    </style>
</x-filament-widgets::widget>