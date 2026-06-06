<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Infrastructure\Eloquent\Opr\OprSetting;
use App\Infrastructure\Eloquent\User\StkEstimate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Domain\Shared\ValueObjects\JapaneseEra;
use App\Constants\Transmission;
use App\Constants\TaxConstants;

/**
 * 見積PDFサービス
 *
 * PDF生成・S3アップロードを担当する。
 * opr_settingsの estimate_pdf_upload_s3 で
 * S3アップロードのON/OFFを切り替え可能。
 */
class EstimatePdfService
{
    /**
     * PDFを生成してS3にアップロードする
     *
     * S3アップロードが無効の場合はPDF生成のみ行い
     * パスの代わりにnullを返す。
     *
     * @param StkEstimate $estimate
     * @return string|null S3パス（アップロード無効時はnull）
     */
    public function generateAndUpload(StkEstimate $estimate): ?string
    {
        // PDF生成
        $pdf = $this->generate($estimate);

        // S3アップロードが無効の場合はスキップ
        if (!$this->isS3Enabled()) {
            return null;
        }

        // S3にアップロード
        $path = $this->buildS3Path($estimate);
        Storage::disk('s3')->put($path, $pdf->output());

        return $path;
    }

    /**
     * PDFを生成してダウンロードレスポンスを返す
     *
     * 画面からのダウンロード用。S3設定に関係なく動作する。
     *
     * @param StkEstimate $estimate
     * @return \Illuminate\Http\Response
     */
    public function download(StkEstimate $estimate)
    {
        $pdf      = $this->generate($estimate);
        $filename = "見積書_{$estimate->estimate_number}.pdf";

        return $pdf->download($filename);
    }

    /**
     * PDFを生成する
     */
    private function generate(StkEstimate $estimate)
    {
        $estimate->load(['car.series', 'car.detail', 'car.dealerFee', 'dealer', 'createdBy']);

        $car    = $estimate->car;
        $detail = $car->detail;

        // 価格計算
        $vehiclePrice = (int) $estimate->vehicle_price;
        $discount     = (int) $estimate->discount;

        if ($estimate->discount_type === 'tax_excluded') {
            $discountedPrice = $vehiclePrice - $discount;
            $consumptionTax  = (int) round($discountedPrice * TaxConstants::CONSUMPTION_TAX_RATE);
        } else {
            $discountedPrice = $vehiclePrice;
            $consumptionTax  = (int) round($vehiclePrice * TaxConstants::CONSUMPTION_TAX_RATE) - $discount;
        }

        $miscTotal  = $estimate->misc_fees_total
            + (int) ($estimate->environmental_performance_tax ?? 0)
            + (int) ($estimate->inspection_registration_fee ?? 0)
            + (int) ($estimate->inspection_registration_fee_exempt ?? 0)
            + (int) ($estimate->trade_in_handling_fee ?? 0)
            + (int) ($estimate->assessment_fee ?? 0);

        $totalPrice = $discountedPrice + $consumptionTax + $miscTotal;

        // 必要書類の分割
        $documents = $estimate->documents ?? [
            ['name' => '印鑑証明'],
            ['name' => '住民票'],
            ['name' => '軽自動車住所証明'],
            ['name' => '納税証明（下取車）'],
            ['name' => '自認書・承諾書'],
            ['name' => '委任状'],
            ['name' => '譲渡証明'],
            ['name' => '保証人印鑑証明'],
        ];
        $half           = (int) ceil(count($documents) / 2);
        $documentsLeft  = array_slice($documents, 0, $half);
        $documentsRight = array_slice($documents, $half);

        // 修復歴ラベル
        $repairHistoryLabel = match($car->repair_history) {
            'none'    => 'なし',
            'minor'   => '軽微あり',
            'major'   => 'あり',
            'unknown' => '不明',
            default   => '-'
        };

        // ミッションラベル
        $transmissionLabel = Transmission::LABELS[$car->transmission] ?? '-';

        // 付属品合計
        $accessoriesTotal = collect($estimate->accessories ?? [])->sum(fn($a) => (int)($a['price'] ?? 0));

        // 税抜金額・非課税対象額
        $taxableAmount    = $discountedPrice + $accessoriesTotal
            + (int) ($estimate->registration_fee ?? 0)
            + (int) ($estimate->garage_cert_fee ?? 0)
            + (int) ($estimate->delivery_fee ?? 0)
            + (int) ($estimate->maintenance_fee ?? 0)
            + (int) ($estimate->environmental_performance_tax ?? 0)
            + (int) ($estimate->inspection_registration_fee ?? 0);

        $nonTaxableAmount = (int) ($estimate->vehicle_tax ?? 0)
            + (int) ($estimate->weight_tax ?? 0)
            + (int) ($estimate->liability_insurance ?? 0)
            + (int) ($estimate->recycle_fee ?? 0)
            + (int) ($estimate->inspection_registration_fee_exempt ?? 0)
            + (int) ($estimate->trade_in_handling_fee ?? 0)
            + (int) ($estimate->assessment_fee ?? 0);

        $html = view('pdf.estimate', [
            'estimate'                => $estimate,
            'car'                     => $car,
            'series'                  => $car->series,
            'detail'                  => $detail,
            'maker'                   => $car->manufacturer,
            'dealer'                  => $estimate->dealer,
            'modelYear'               => new JapaneseEra($car->model_year),
            'vehiclePrice'            => $vehiclePrice,
            'discount'                => $discount,
            'discountedPrice'         => $discountedPrice,
            'consumptionTax'          => $consumptionTax,
            'miscTotal'               => $miscTotal,
            'totalPrice'              => $totalPrice,
            'accessoriesTotal'        => $accessoriesTotal,
            'taxableAmount'           => $taxableAmount,
            'nonTaxableAmount'        => $nonTaxableAmount,
            'documentsLeft'           => $documentsLeft,
            'documentsRight'          => $documentsRight,
            'repairHistoryLabel'      => $repairHistoryLabel,
            'transmissionLabel'       => $transmissionLabel,
            'taxRateDisplay'          => TaxConstants::CONSUMPTION_TAX_RATE_DISPLAY,
        ])->render();

        $html = "\xEF\xBB\xBF" . $html;

        $pdf = Pdf::setOptions([
            'defaultFont'            => 'ipaexgothic',
            'fontDir'                => base_path('vendor/dompdf/dompdf/lib/fonts'),
            'fontCache'              => base_path('vendor/dompdf/dompdf/lib/fonts'),
            'enable_php'             => true,
            'isRemoteEnabled'        => true,
            'enable_font_subsetting' => true,
            'dpi'                    => 96,
        ])->loadHTML($html, 'UTF-8');

        return $pdf->setPaper('a4', 'portrait');
    }

    /**
     * S3アップロードが有効かどうか
     */
    private function isS3Enabled(): bool
    {
        return (bool) OprSetting::getValue('estimate_pdf_upload_s3', '1');
    }

    /**
     * S3パスを生成する
     * 例：estimates/2026/06/EST-20260605-0001.pdf
     */
    private function buildS3Path(StkEstimate $estimate): string
    {
        return sprintf(
            'estimates/%s/%s/%s.pdf',
            now()->format('Y'),
            now()->format('m'),
            $estimate->estimate_number,
        );
    }
}