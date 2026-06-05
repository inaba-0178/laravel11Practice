<x-filament-widgets::widget>
    <div class="daw-widget">
        <div class="daw-wrap">

            {{-- タイトル・期間切り替え --}}
            <div class="daw-header">
                <h3 class="daw-title">アクセス・問い合わせ分析</h3>
                <div class="daw-controls">

                    {{-- 問い合わせ切り替え --}}
                    <div class="daw-toggle-group">
                        <button
                            class="daw-toggle {{ $this->inquiryMode === 'all' ? 'is-active' : '' }}"
                            wire:click="setInquiryMode('all')"
                        >合計</button>
                        <button
                            class="daw-toggle {{ $this->inquiryMode === 'view' ? 'is-active' : '' }}"
                            wire:click="setInquiryMode('view')"
                        >閲覧数</button>
                        <button
                            class="daw-toggle {{ $this->inquiryMode === 'favorite' ? 'is-active' : '' }}"
                            wire:click="setInquiryMode('favorite')"
                        >お気に入り</button>
                        <button
                            class="daw-toggle {{ $this->inquiryMode === 'reservation' ? 'is-active' : '' }}"
                            wire:click="setInquiryMode('reservation')"
                        >予約数</button>
                        <button
                            class="daw-toggle {{ $this->inquiryMode === 'inquiry' ? 'is-active' : '' }}"
                            wire:click="setInquiryMode('inquiry')"
                        >在庫確認・見積</button>
                    </div>

                    {{-- 期間切り替え --}}
                    <div class="daw-period-group">
                        <button
                            class="daw-period {{ $this->period === 'weekly' ? 'is-active' : '' }}"
                            wire:click="setPeriod('weekly')"
                        >週間</button>
                        <button
                            class="daw-period {{ $this->period === 'monthly' ? 'is-active' : '' }}"
                            wire:click="setPeriod('monthly')"
                        >月間</button>
                        <button
                            class="daw-period {{ $this->period === 'yearly' ? 'is-active' : '' }}"
                            wire:click="setPeriod('yearly')"
                        >年間</button>
                    </div>

                    {{-- 前後移動 --}}
                    <div class="daw-nav">
                        <button class="daw-nav__btn" wire:click="prevPeriod">‹</button>
                        <span class="daw-nav__label">{{ $this->getPeriodLabel() }}</span>
                        <button
                            class="daw-nav__btn {{ $this->offset >= 0 ? 'is-disabled' : '' }}"
                            wire:click="nextPeriod"
                            {{ $this->offset >= 0 ? 'disabled' : '' }}
                        >›</button>
                    </div>
                </div>
            </div>

            {{-- サマリー --}}
            @php $analytics = $this->getAnalytics(); @endphp
            <div class="daw-summary">
                <div class="daw-summary__item">
                    <span class="daw-summary__label">閲覧数</span>
                    <span class="daw-summary__value daw-summary__value--blue">{{ number_format($analytics['summary']['views']) }}</span>
                </div>
                <div class="daw-summary__item">
                    <span class="daw-summary__label">お気に入り</span>
                    <span class="daw-summary__value daw-summary__value--pink">{{ number_format($analytics['summary']['favorites']) }}</span>
                </div>
                @if($this->inquiryMode === 'all' || $this->inquiryMode === 'reservation')
                <div class="daw-summary__item">
                    <span class="daw-summary__label">予約数</span>
                    <span class="daw-summary__value daw-summary__value--green">{{ number_format($analytics['summary']['reservations']) }}</span>
                </div>
                @endif
                @if($this->inquiryMode === 'all' || $this->inquiryMode === 'inquiry')
                <div class="daw-summary__item">
                    <span class="daw-summary__label">在庫確認・見積</span>
                    <span class="daw-summary__value daw-summary__value--yellow">{{ number_format($analytics['summary']['inquiries']) }}</span>
                </div>
                @endif
            </div>

            {{-- グラフ --}}
            <div class="daw-chart-wrap">
                <div
                    x-data
                    x-init="
                        if (!window.Chart) {
                            const script = document.createElement('script');
                            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
                            script.onload = () => initChart();
                            document.head.appendChild(script);
                        } else {
                            initChart();
                        }

                        function initChart() {
                            const ctx = document.getElementById('dealerAnalyticsChart');
                            if (!ctx) return;

                            if (window._dealerAnalyticsChart) {
                                window._dealerAnalyticsChart.destroy();
                            }

                            const labels      = {{ json_encode($analytics['labels']) }};
                            const viewCounts  = {{ json_encode($analytics['viewCounts']) }};
                            const favCounts   = {{ json_encode($analytics['favCounts']) }};
                            const resCounts   = {{ json_encode($analytics['resCounts']) }};
                            const inqCounts   = {{ json_encode($analytics['inqCounts']) }};
                            const inquiryMode = '{{ $this->inquiryMode }}';

                            const datasets = [];

                            if (inquiryMode === 'all' || inquiryMode === 'view') {
                                datasets.push({
                                    label:           '閲覧数',
                                    data:            viewCounts,
                                    borderColor:     '#3b82f6',
                                    backgroundColor: 'rgba(59,130,246,0.08)',
                                    borderWidth:     2,
                                    pointRadius:     3,
                                    tension:         0.3,
                                    fill:            inquiryMode === 'view',
                                    yAxisID:         'y',
                                });
                            }

                            if (inquiryMode === 'all' || inquiryMode === 'favorite') {
                                datasets.push({
                                    label:           'お気に入り',
                                    data:            favCounts,
                                    borderColor:     '#dc5078',
                                    backgroundColor: 'rgba(220,80,120,0.08)',
                                    borderWidth:     2,
                                    pointRadius:     3,
                                    tension:         0.3,
                                    fill:            inquiryMode === 'favorite',
                                    yAxisID:         'y',
                                });
                            }

                            if (inquiryMode === 'all' || inquiryMode === 'reservation') {
                                datasets.push({
                                    label:       '予約数',
                                    data:        resCounts,
                                    borderColor: '#4ade80',
                                    borderDash:  [6, 3],
                                    borderWidth: 2,
                                    pointRadius: 3,
                                    tension:     0.3,
                                    fill:        false,
                                    yAxisID:     'y',
                                });
                            }

                            if (inquiryMode === 'all' || inquiryMode === 'inquiry') {
                                datasets.push({
                                    label:       '在庫確認・見積',
                                    data:        inqCounts,
                                    borderColor: '#fbbf24',
                                    borderDash:  [2, 2],
                                    borderWidth: 2,
                                    pointRadius: 3,
                                    tension:     0.3,
                                    fill:        false,
                                    yAxisID:     'y',
                                });
                            }

                            window._dealerAnalyticsChart = new Chart(ctx, {
                                type: 'line',
                                data: { labels, datasets },
                                options: {
                                    responsive:          true,
                                    maintainAspectRatio: false,
                                    interaction: { mode: 'index', intersect: false },
                                    plugins: {
                                        legend: {
                                            labels: { color: '#9ca3af', font: { size: 11 }, boxWidth: 20 }
                                        },
                                    },
                                    scales: {
                                        x: {
                                            ticks: { color: '#6b7280', font: { size: 10 }, maxRotation: 45 },
                                            grid:  { color: 'rgba(55,65,81,0.4)' },
                                        },
                                        y: {
                                            position: 'left',
                                            min:      0,
                                            ticks: {
                                                color:    '#9ca3af',
                                                font:     { size: 10 },
                                                stepSize: 1,
                                                callback: (val) => Number.isInteger(val) ? val : null,
                                            },
                                            grid:  { color: 'rgba(55,65,81,0.4)' },
                                            title: { display: true, text: '件数', color: '#9ca3af', font: { size: 10 } },
                                        },
                                    },
                                },
                            });
                        }
                    "
                    style="width:100%;height:100%;"
                >
                    <canvas id="dealerAnalyticsChart" style="width:100%;height:100%;"></canvas>
                </div>
            </div>

        </div>
    </div>

    <style>
        .daw-widget { font-family: sans-serif; color: #e5e7eb; }

        .daw-wrap {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 10px;
            overflow: hidden;
        }

        /* ヘッダー */
        .daw-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid #374151;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .daw-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #f9fafb;
            margin: 0;
        }
        .daw-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        /* 問い合わせ切り替え */
        .daw-toggle-group,
        .daw-period-group {
            display: flex;
            gap: 2px;
            background: #111827;
            border-radius: 6px;
            padding: 2px;
        }
        .daw-toggle,
        .daw-period {
            font-size: 0.7rem;
            padding: 3px 10px;
            border: none;
            border-radius: 4px;
            background: transparent;
            color: #9ca3af;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .daw-toggle:hover,
        .daw-period:hover  { color: #f9fafb; }
        .daw-toggle.is-active,
        .daw-period.is-active {
            background: #374151;
            color: #f9fafb;
        }

        /* 前後ナビ */
        .daw-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .daw-nav__label { font-size: 0.75rem; color: #9ca3af; }
        .daw-nav__btn {
            width: 24px;
            height: 24px;
            border: 1px solid #374151;
            border-radius: 50%;
            background: transparent;
            color: #9ca3af;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }
        .daw-nav__btn:hover:not(:disabled) { border-color: #6366f1; color: #6366f1; }
        .daw-nav__btn.is-disabled,
        .daw-nav__btn:disabled { opacity: 0.3; cursor: not-allowed; }

        /* サマリー */
        .daw-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 1px;
            background: #374151;
            border-bottom: 1px solid #374151;
        }
        .daw-summary__item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            background: #111827;
            padding: 0.75rem 1rem;
        }
        .daw-summary__label { font-size: 0.7rem; color: #9ca3af; }
        .daw-summary__value {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .daw-summary__value--blue   { color: #3b82f6; }
        .daw-summary__value--pink   { color: #dc5078; }
        .daw-summary__value--green  { color: #4ade80; }
        .daw-summary__value--yellow { color: #fbbf24; }

        /* グラフ */
        .daw-chart-wrap {
            padding: 1rem 1.25rem;
            height: 280px;
            position: relative;
        }
    </style>
</x-filament-widgets::widget>