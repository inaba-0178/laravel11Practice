<x-filament-widgets::widget>
    <div class="dbs-widget">
        <div class="dbs-wrap">

            {{-- タイトル --}}
            <h3 class="dbs-title">一括登録承認状況</h3>

            {{-- サマリー --}}
            @php $summary = $this->getSummary(); @endphp
            <div class="dbs-summary__grid">
                <div class="dbs-summary__item">
                    <span class="dbs-summary__label">バッチ数</span>
                    <span class="dbs-summary__value">{{ $summary['total'] }}件</span>
                </div>
                <div class="dbs-summary__item">
                    <span class="dbs-summary__label">承認待ちあり</span>
                    <span class="dbs-summary__value dbs-summary__value--warning">{{ $summary['pending'] }}件</span>
                </div>
                <div class="dbs-summary__item">
                    <span class="dbs-summary__label">差し戻しあり</span>
                    <span class="dbs-summary__value dbs-summary__value--danger">{{ $summary['rejected'] }}件</span>
                </div>
            </div>

            {{-- 履歴テーブル --}}
            @php $batches = $this->getBatches(); @endphp
            <div class="dbs-table-section">
                @if(empty($batches))
                    <p class="dbs-empty">現在なし</p>
                @else
                <div class="dbs-scroll">
                    <table class="dbs-table">
                        <thead>
                            <tr>
                                <th>返却日時</th>
                                <th>ステータス</th>
                                <th>登録日時</th>
                                <th>合計</th>
                                <th>承認待ち</th>
                                <th>承認済み</th>
                                <th>差し戻し</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batches as $batch)
                            @php
                                $isRejected  = in_array($batch['status'], ['rejected', 'partial_rejected']);
                                $isApproved  = in_array($batch['status'], ['published', 'approved_pending']);
                                $rowClass    = $isRejected ? 'is-rejected' : ($isApproved ? 'is-approved' : '');
                                $statusClass = $isRejected ? 'is-danger' : ($isApproved ? 'is-success' : 'is-warning');
                            @endphp
                            <tr class="dbs-table__row {{ $rowClass }}">
                                <td class="dbs-table__td">{{ $batch['updatedAt'] ?? '---' }}</td>
                                <td class="dbs-table__td">
                                    <span class="dbs-badge dbs-badge--{{ $statusClass }}">
                                        {{ $batch['statusLabel'] }}
                                    </span>
                                </td>
                                <td class="dbs-table__td">{{ $batch['uploadedAt'] }}</td>
                                <td class="dbs-table__td">{{ $batch['totalCount'] }}台</td>
                                <td class="dbs-table__td dbs-table__td--warning">{{ $batch['pendingCount'] }}台</td>
                                <td class="dbs-table__td dbs-table__td--success">{{ $batch['approvedCount'] }}台</td>
                                <td class="dbs-table__td dbs-table__td--danger">{{ $batch['rejectedCount'] }}台</td>
                                <td class="dbs-table__td">
                                    <a href="{{ $batch['detailUrl'] }}" class="dbs-link">詳細</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

        </div>
    </div>

    <style>
        .dbs-widget { font-family: sans-serif; color: #e5e7eb; }

        .dbs-wrap {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 10px;
            overflow: hidden;
        }

        .dbs-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #f9fafb;
            margin: 0;
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid #374151;
        }

        /* サマリー */
        .dbs-summary__grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;           /* ← gapを1pxから8pxに */
            background: #1f2937; /* ← 背景色を変更 */
            border-bottom: 1px solid #374151;
            padding: 0.75rem 1rem; /* ← padding追加 */
        }

        .dbs-summary__item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            background: #111827;
            padding: 0.75rem 1rem;
        }
        .dbs-summary__label {
            font-size: 0.7rem;
            color: #9ca3af;
        }
        .dbs-summary__value {
            font-size: 1rem;
            font-weight: 600;
            color: #f9fafb;
        }
        .dbs-summary__value--warning { color: #fbbf24; }
        .dbs-summary__value--danger  { color: #f87171; }

        /* テーブルセクション */
        .dbs-table-section {
            background: #111827;
            padding: 0.75rem 1rem; /* ← padding追加 */
        }
        .dbs-scroll {
            overflow-y: auto;
            max-height: 300px;
            border: 1px solid #374151; /* ← 枠線追加 */
            border-radius: 8px;        /* ← 角丸追加 */
        }
        .dbs-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.7rem;
        }
        .dbs-table thead th {
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
        .dbs-table__row {
            border-bottom: 1px solid #1a1a2e;
            transition: background 0.1s;
        }
        .dbs-table__row:hover       { background: #1f2937; }
        .dbs-table__row.is-rejected { background: rgba(239,68,68,0.08); }
        .dbs-table__row.is-approved { background: rgba(34,197,94,0.08); }
        .dbs-table__td {
            padding: 0.5rem 0.75rem;
            color: #d1d5db;
            white-space: nowrap;
        }
        .dbs-table__td--warning { color: #fbbf24; }
        .dbs-table__td--success { color: #4ade80; }
        .dbs-table__td--danger  { color: #f87171; }

        .dbs-badge {
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 500;
        }
        .dbs-badge--is-warning { background: rgba(251,191,36,0.15);  color: #fbbf24; }
        .dbs-badge--is-success { background: rgba(34,197,94,0.15);   color: #4ade80; }
        .dbs-badge--is-danger  { background: rgba(239,68,68,0.15);   color: #f87171; }

        .dbs-link {
            font-size: 0.7rem;
            color: #818cf8;
            text-decoration: none;
            border: 1px solid #4f46e5;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .dbs-link:hover { background: rgba(99,102,241,0.1); }

        .dbs-empty {
            padding: 2rem;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
        }
    </style>
</x-filament-widgets::widget>