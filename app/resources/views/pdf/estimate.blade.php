    <meta charset="UTF-8">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'ipaexgothic',sans-serif; }
        body { font-family:'ipaexgothic',sans-serif; font-size:10px; color:#000; padding:6px 8px; }

        /* ===== 共通 ===== */
        table { border-collapse:collapse; width:100%; }
        td { border:1px solid #000; padding:3px 5px; }
        .section { margin-bottom:6px; }

        /* ===== カラー ===== */
        .bg-blue  { background:#d0d8e8; }
        .bg-gray  { background:#e8e8e8; }
        .bg-none  { background:#fff; }

        /* ===== テキスト ===== */
        .tac  { text-align:center; }
        .tar  { text-align:right; }
        .tal  { text-align:left; }
        .bold { font-weight:bold; }
        .red  { color:#cc0000; }
        .fs7  { font-size:7px; }
        .fs8  { font-size:8px; }
        .fs9  { font-size:9px; }
        .fs10 { font-size:10px; }
        .fs12 { font-size:12px; }
        .fs13 { font-size:13px; }
        .fs14 { font-size:14px; }

        /* ===== ボーダーなし ===== */
        .nb { border:none; }
        .nb-t { border-top:none; }
        .nb-b { border-bottom:none; }

        /* ===== 縦書き ===== */
        .vr {
            writing-mode:vertical-rl;
            text-orientation:mixed;
            text-align:center;
            font-size:7px;
            background:#d0d8e8;
            width:14px;
        }

        /* ===== ヘッダー ===== */
        .header-wrap { display:table; width:100%; }
        .header-title { display:table-cell; width:35%; vertical-align:middle; }
        .header-info  { display:table-cell; width:65%; vertical-align:top; }
        .title-box {
            border:2px solid #000;
            padding:4px 10px;
            font-size:14px;
            font-weight:bold;
            letter-spacing:3px;
            display:inline-block;
        }

        /* ===== 顧客情報 ===== */
        .customer-name { font-size:12px; font-weight:bold; }

        /* ===== 見積金額 ===== */
        .amount-wrap  { display:table; width:100%; }
        .amount-left  { display:table-cell; width:38%; vertical-align:top; }
        .amount-gap   { display:table-cell; width:3%; }
        .amount-right { display:table-cell; width:59%; vertical-align:top; }
        .amount-box   { border:2px solid #000; display:table; width:100%; margin-bottom:3px; }
        .amount-label {
            display:table-cell;
            background:#d0d8e8;
            padding:4px 6px;
            font-weight:bold;
            font-size:10px;
            width:40%;
            vertical-align:middle;
        }
        .amount-value {
            display:table-cell;
            font-size:14px;
            font-weight:bold;
            padding:4px 6px;
            text-align:right;
            vertical-align:middle;
        }

        /* ===== 明細 ===== */
        .detail-space { border:none; height:8px; }
        .detail-row-space { height:16px; }
        .detail-row-space2 { height:20px; }

        /* ===== リサイクル ===== */
        .recycle-table { width:40%; }

        /* ===== ディーラー ===== */
        .dealer-section { text-align:right; margin-bottom:8px; }
        .dealer-name    { font-size:13px; font-weight:bold; }
        .dealer-info    { font-size:9px; line-height:1.8; }

        /* ===== 必要書類 ===== */
        .docs-wrap  { display:table; width:100%; }
        .docs-left  { display:table-cell; width:50%; vertical-align:top; }
        .docs-right { display:table-cell; width:50%; vertical-align:top; padding-left:8px; font-size:9px; }
        .docs-table { width:90%; }
    </style>


    {{-- ===== ヘッダー ===== --}}
    <div class="section header-wrap">
        <div class="header-title">
            <div class="title-box">{{ $documentTitle }}</div>
        </div>
        <div class="header-info">
            <table>
                <tr>
                    <td class="bg-blue tac">日付</td>
                    <td class="bg-blue tac">販売区分</td>
                    <td class="bg-blue tac">担当</td>
                    <td class="bg-blue tac">{{ $documentNumberLabel }}</td>
                </tr>
                <tr>
                    <td class="tac">{{ $estimate->created_at->format('Y.m.d') }}</td>
                    <td class="tac">中古車</td>
                    <td class="tac">{{ $estimate->createdBy?->name ?? '-' }}</td>
                    <td class="tac">{{ $estimate->estimate_number }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ===== 顧客情報 ===== --}}
    <div class="section">
        <table style="table-layout:fixed;">
            <colgroup>
                <col style="width:50px;">
                <col style="width:180px;">
                <col style="width:60px;">
                <col style="width:120px;">
                <col style="width:70px;">
            </colgroup>
            <tr>
                <td class="bg-blue" rowspan="2">お名前</td>
                <td rowspan="2">
                    <span class="customer-name">{{ $estimate->customer_name ?? $estimate->customer_nickname ?? '-' }}</span>&nbsp;様
                    @if($estimate->customer_name && $estimate->customer_nickname)
                        <br><span class="fs9">（{{ $estimate->customer_nickname }}）</span>
                    @endif
                </td>
                <td class="bg-blue">生年月日</td>
                <td>{{ $estimate->customer_birth_date?->format('Y.m.d') ?? '-' }}</td>
                <td rowspan="4"></td>
            </tr>
            <tr>
                <td class="bg-blue">電話番号</td>
                <td>{{ $estimate->customer_phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="bg-blue" rowspan="2">ご住所</td>
                <td rowspan="2">
                    @if($postalCode)〒{{ $postalCode }}<br>@endif
                    @if($estimate->customer_address){{ $estimate->customer_address }}@endif
                </td>
                <td class="bg-blue">勤務先等</td>
                <td>{{ $estimate->customer_workplace ?? '-' }}</td>
            </tr>
            <tr>
                <td class="bg-blue">連絡先Tel</td>
                <td>{{ $estimate->customer_contact_phone ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- ===== 車両情報 ===== --}}
    <div class="section">
        <table>
            <tr>
                <td class="bg-blue tac" style="width:60px;">メーカー</td>
                <td class="bg-blue tac" colspan="3">車名・仕様</td>
                <td class="bg-blue tac" style="width:55px;">年式</td>
                <td class="bg-blue tac" style="width:55px;">排気量</td>
                <td class="bg-blue tac" style="width:55px;">ミッション</td>
                <td class="bg-blue tac" style="width:55px;">車体色</td>
            </tr>
            <tr>
                <td class="tac">{{ $maker?->name ?? '-' }}</td>
                <td class="tac" colspan="3">{{ $series?->series_name ?? '-' }}</td>
                <td class="tac">{{ $modelYear }}</td>
                <td class="tac">{{ $displacementLabel }}</td>
                <td class="tac">{{ $transmissionLabel }}</td>
                <td class="tac">{{ $car->color ?? '-' }}</td>
            </tr>
            <tr>
                <td class="bg-blue tac">型式</td>
                <td class="bg-blue tac">車台番号</td>
                <td class="bg-blue tac">登録番号</td>
                <td class="bg-blue tac">走行距離</td>
                <td class="bg-blue tac">車検日</td>
                <td class="bg-blue tac">修復歴</td>
                <td class="bg-blue tac">記録簿</td>
                <td class="bg-blue tac">在庫番号</td>
            </tr>
            <tr>
                <td class="tac">{{ $estimate->vehicle_model ?? '-' }}</td>
                <td class="tac">{{ $estimate->chassis_number ?? '-' }}</td>
                <td class="tac">{{ $estimate->registration_number ?? '-' }}</td>
                <td class="tac">{{ $mileageLabel }}</td>
                <td class="tac">{{ $detail?->inspection_expire_date?->format('Y.m.d') ?? '-' }}</td>
                <td class="tac">{{ $repairHistoryLabel }}</td>
                <td class="tac">{{ $serviceRecordLabel }}</td>
                <td class="tac">{{ $car->stock_number ?? 'STK-' . $car->id }}</td>
            </tr>
        </table>
    </div>

    {{-- ===== 見積金額・下取車 ===== --}}
    <div class="section amount-wrap">
        <div class="amount-left">
            <div class="amount-box">
                <div class="amount-label">お見積金額</div>
                <div class="amount-value">{{ number_format($totalPrice) }}円</div>
            </div>
            @if($estimate->recycle_fee)
            <div class="fs7" style="margin-bottom:3px;">
                ※上記のお見積金額は、リサイクル料金{{ number_format($estimate->recycle_fee) }}円が含まれています。
            </div>
            @endif
            <div class="fs9">販売仕様　○ 現状渡し　○ 整備付　○ 保証付　○ 他</div>
        </div>
        <div class="amount-gap"></div>
        <div class="amount-right">
            <table>
                <tr>
                    <td class="bg-blue tac" colspan="3">下取車名（型式等）</td>
                    <td class="bg-blue tac">年式</td>
                    <td class="bg-blue tac">車検日</td>
                    <td class="bg-blue tac">走行距離</td>
                    <td class="bg-blue tac">車体色</td>
                </tr>
                <tr>
                    <td class="tac" colspan="3">{{ $estimate->trade_in_name ?? '-' }}</td>
                    <td class="tac">{{ $estimate->trade_in_model_year ?? '-' }}</td>
                    <td class="tac">{{ $estimate->trade_in_inspection_date?->format('Y.m.d') ?? '-' }}</td>
                    <td class="tac">{{ $estimate->trade_in_mileage ? number_format($estimate->trade_in_mileage) . 'km' : '-' }}</td>
                    <td class="tac">{{ $estimate->trade_in_color ?? '-' }}</td>
                </tr>
            </table>
            <div class="fs7" style="margin-top:3px;">
                ※課税対象額({{ $taxRateDisplay }}%) {{ number_format($taxableAmount) }}円　消費税({{ $taxRateDisplay }}%) {{ number_format($consumptionTax) }}円　非課税対象額 {{ number_format($nonTaxableAmount) }}円<br>
                ※現行の税法上、新車を除く自動車税と自賠責保険の未経過分は課税対象となります
            </div>
        </div>
    </div>

    {{-- ===== 明細セクション ===== --}}
    <div class="section">
        <table class="fs9">
            <tr>
                <td class="bg-blue tac bold" colspan="3">車両明細</td>
                <td class="bg-blue tac bold" colspan="3">諸費用明細</td>
                <td class="bg-blue tac bold" colspan="3">付属品／特別仕様明細</td>
            </tr>
            <tr>
                <td>※車両本体価格</td>
                <td class="tar">{{ number_format($vehiclePrice) }}</td>
                <td rowspan="7" style="width:50px;"></td>
                <td rowspan="6" class="vr">税金・保険料</td>
                <td>自動車税</td>
                <td class="tar">{{ number_format($estimate->vehicle_tax) }}</td>
                <td rowspan="13" class="vr">品名・仕様</td>
                @if($estimate->accessories && count($estimate->accessories) > 0)
                <td>{{ $estimate->accessories[0]['name'] ?? '' }}</td>
                <td class="tar">{{ isset($estimate->accessories[0]) ? number_format($estimate->accessories[0]['price']) : '' }}</td>
                @else
                <td></td><td></td>
                @endif
            </tr>
            <tr>
                <td>値引等</td>
                <td class="tar red">{{ $discount > 0 ? '-' . number_format($discount) : '-' }}</td>
                <td>環境性能割</td>
                <td class="tar">{{ number_format($estimate->environmental_performance_tax ?? 0) }}</td>
                <td>{{ $estimate->accessories[1]['name'] ?? '' }}</td>
                <td class="tar">{{ isset($estimate->accessories[1]) ? number_format($estimate->accessories[1]['price']) : '' }}</td>
            </tr>
            <tr>
                <td>本体課税対象額</td>
                <td class="tar">{{ number_format($discountedPrice) }}</td>
                <td>重量税</td>
                <td class="tar">{{ number_format($estimate->weight_tax) }}</td>
                <td>{{ $estimate->accessories[2]['name'] ?? '' }}</td>
                <td class="tar">{{ isset($estimate->accessories[2]) ? number_format($estimate->accessories[2]['price']) : '' }}</td>
            </tr>
            <tr>
                <td>付属品/特別仕様</td>
                <td class="tar">{{ $accessoriesTotal > 0 ? number_format($accessoriesTotal) : '-' }}</td>
                <td>自賠責（{{ $liabilityInsuranceMonths }}）</td>
                <td class="tar">{{ number_format($estimate->liability_insurance) }}</td>
                <td>{{ $estimate->accessories[3]['name'] ?? '' }}</td>
                <td class="tar">{{ isset($estimate->accessories[3]) ? number_format($estimate->accessories[3]['price']) : '' }}</td>
            </tr>
            <tr>
                <td colspan="2" class="detail-row-space"></td>
                <td class="nb detail-space"></td>
                <td class="nb"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="bg-gray bold">車両販売合計</td>
                <td class="tar bg-gray bold">{{ number_format($discountedPrice + $accessoriesTotal) }}</td>
                <td class="bg-gray bold tar">税金／保険料計</td>
                <td class="tar bg-gray bold">{{ number_format($taxInsuranceTotal) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="bg-gray bold">諸費用合計</td>
                <td class="tar bg-gray bold">{{ number_format($miscTotal) }}</td>
                <td rowspan="7" class="vr">課税対象</td>
                <td>検査／登録／届出</td>
                <td class="tar">{{ number_format($estimate->inspection_registration_fee ?? 0) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2" class="detail-row-space2"></td>
                <td class="bg-blue tar fs7 bold">課税対象({{ $taxRateDisplay }}%)</td>
                <td>車庫証明手続費用</td>
                <td class="tar">{{ number_format($estimate->garage_cert_fee) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="bg-blue">税抜金額</td>
                <td class="tar">{{ number_format($taxableAmount) }}</td>
                <td class="tar">{{ number_format($taxableAmount) }}</td>
                <td>下取車諸手続</td>
                <td class="tar">{{ number_format($estimate->trade_in_handling_fee ?? 0) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="bg-blue">消費税({{ $taxRateDisplay }}%)</td>
                <td class="tar">{{ number_format($consumptionTax) }}</td>
                <td class="bg-blue tac fs7 bold">非課税対象</td>
                <td>納車費用</td>
                <td class="tar">{{ number_format($estimate->delivery_fee) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="bg-blue bold">総額</td>
                <td class="tar bg-blue bold">{{ number_format($totalPrice) }}</td>
                <td class="tar">{{ number_format($nonTaxableAmount) }}</td>
                <td>査定料</td>
                <td class="tar">{{ number_format($estimate->assessment_fee ?? 0) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>下取車価格</td>
                <td class="tar">{{ $estimate->trade_in_price ? number_format($estimate->trade_in_price) : '-' }}</td>
                <td rowspan="3"></td>
                <td class="nb detail-space"></td>
                <td class="nb"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>頭金/現金/他</td>
                <td class="tar">{{ $estimate->down_payment ? number_format($estimate->down_payment) : '-' }}</td>
                <td class="bg-gray bold tar">課税対象計</td>
                <td class="tar bg-gray bold">{{ number_format($taxableSubTotal) }}</td>
                <td class="bg-gray bold tac" colspan="2">付属品／特別仕様合計　{{ number_format($accessoriesTotal) }}</td>
            </tr>
            <tr>
                <td class="bg-blue bold">残金/所要資金</td>
                <td class="tar bg-blue bold">{{ $estimate->remaining_amount ? number_format($estimate->remaining_amount) : '-' }}</td>
                <td rowspan="6" class="vr">非課税</td>
                <td>検査／登録／届出</td>
                <td class="tar">{{ number_format($estimate->inspection_registration_fee_exempt ?? 0) }}</td>
                <td rowspan="6" class="vr">備考</td>
                <td rowspan="6" colspan="2"></td>
            </tr>
            <tr>
                <td colspan="3" class="fs7">※上記の金額は、リサイクル料金を含みます。</td>
                <td>車庫証明証紙</td>
                <td class="tar">-</td>
            </tr>
            <tr>
                <td colspan="3" class="bg-blue tac bold">クレジットお支払いプラン</td>
                <td>下取車諸手続き</td>
                <td class="tar">-</td>
            </tr>
            <tr>
                <td>支払回数</td>
                <td colspan="2">{{ $creditMonthsLabel }}</td>
                <td class="nb detail-space"></td>
                <td class="nb"></td>
            </tr>
            <tr>
                <td>月払</td>
                <td colspan="2">{{ $monthlyPaymentLabel }}</td>
                <td class="bg-gray bold tar">非課税計</td>
                <td class="tar bg-gray bold">{{ number_format($nonTaxableSubTotal) }}</td>
            </tr>
            <tr>
                <td>賞与　月</td>
                <td colspan="2">{{ $bonusPaymentLabel }}</td>
                <td class="bg-blue tac bold" colspan="3">諸費用合計　{{ number_format($miscTotal) }}</td>
            </tr>
        </table>
    </div>

    {{-- ===== リサイクル料金 ===== --}}
    <div class="section">
        <table class="recycle-table fs9">
            <tr>
                <td class="bg-blue">リサイクル料金（預託金）</td>
                <td class="tar bold" style="width:80px;">{{ number_format($estimate->recycle_fee) }}円</td>
            </tr>
        </table>
    </div>

    {{-- ===== ディーラー情報 ===== --}}
    <div class="section dealer-section">
        <div class="dealer-name">{{ $dealer?->name ?? '-' }}</div>
        <div class="dealer-info">
            @if($dealer?->tax_number){{ $dealer->tax_number }}<br>@endif
            @if($dealerPostalCode)〒{{ $dealerPostalCode }}@endif　{{ $dealer?->city }}{{ $dealer?->address_detail }}<br>
            TEL {{ $dealer?->phone ?? '-' }}
            @if($dealer?->fax)　FAX {{ $dealer->fax }}@endif
        </div>
    </div>

    {{-- ===== 必要書類 ===== --}}
    <div class="docs-wrap">
        <div class="docs-left">
            <table class="docs-table fs9">
                <tr>
                    <td rowspan="{{ max(count($documentsLeft), count($documentsRight)) }}" class="vr">必要書類</td>
                    <td>{{ $documentsLeft[0]['name'] ?? '' }}</td>
                    <td>{{ $documentsRight[0]['name'] ?? '' }}</td>
                </tr>
                @for($i = 1; $i < max(count($documentsLeft), count($documentsRight)); $i++)
                <tr>
                    <td>{{ $documentsLeft[$i]['name'] ?? '' }}</td>
                    <td>{{ $documentsRight[$i]['name'] ?? '' }}</td>
                </tr>
                @endfor
            </table>
        </div>
        <div class="docs-right">
            @if($estimate->notes)
            <strong>備考：</strong>{{ $estimate->notes }}
            @endif
        </div>
    </div>

    {{-- ===== 約款誘導テキスト（契約書のみ） ===== --}}
    @if($showContractNote)
    <div class="section fs8" style="margin-top:4px; border:1px solid #000; padding:4px 6px;">
        ※本契約の条項については、次ページ（裏面）の自動車売買注文書特約条項（約款）を必ずご確認ください。
    </div>
    @endif

    {{-- ===== 署名捺印欄（契約書のみ） ===== --}}
    @if($showSignatureArea)
    <div class="section" style="margin-top:6px;">
        <table>
            <tr>
                <td class="bg-blue bold" style="width:120px;">【注文ご署名捺印欄】</td>
                <td class="fs8">
                    上記内容および裏面（または2枚目）の約款に同意のうえ、車両を注文します。<br>
                    注文日：{{ now()->format('Y') }}年　＿月　＿日　　ご署名：＿＿＿＿＿＿＿＿＿＿＿＿＿（実印）
                </td>
            </tr>
        </table>
    </div>
    @endif