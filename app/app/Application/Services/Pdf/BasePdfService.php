<?php

declare(strict_types=1);

namespace App\Application\Services\Pdf;

use App\Infrastructure\Eloquent\User\StkEstimate;
use App\Domain\Shared\Enums\PdfDocumentType;
use App\Domain\Shared\ValueObjects\JapaneseEra;
use App\Domain\Shared\Enums\RepairHistory;
use App\Constants\Transmission;
use App\Constants\TaxConstants;
use Barryvdh\DomPDF\Facade\Pdf;

abstract class BasePdfService
{
    // ===== 定数 =====

    /** PDF日本語フォント名（IPAexゴシック） */
    protected const PDF_FONT_JAPANESE = 'ipaexgothic';

    /** dompdfフォントディレクトリパス */
    protected const PDF_FONT_DIR = 'vendor/dompdf/dompdf/lib/fonts';

    /** PDF描画解像度（dots per inch） */
    protected const PDF_DPI = 96;

    /** 郵便番号の桁数 */
    protected const POSTAL_CODE_LENGTH = 7;

    /** 郵便番号のハイフン挿入位置 */
    protected const POSTAL_CODE_HYPHEN_POSITION = 3;

    // ===== protected メソッド =====

    /**
     * ドキュメント種別に応じたメタ情報を返す
     */
    protected function buildDocumentMeta(string $documentType): array
    {
        return [
            'documentTitle'       => PdfDocumentType::TITLES[$documentType],
            'documentNumberLabel' => PdfDocumentType::NUMBER_LABELS[$documentType],
            'showSignatureArea'   => PdfDocumentType::SHOW_SIGNATURE_AREA[$documentType],
            'showContractNote'    => PdfDocumentType::SHOW_CONTRACT_NOTE[$documentType],
        ];
    }

    // ===== private メソッド =====

    /**
     * 価格計算
     */
    private function calcPrices(StkEstimate $estimate): array
    {
        $vehiclePrice = (int) $estimate->vehicle_price;
        $discount     = (int) $estimate->discount;

        if ($estimate->discount_type === 'tax_excluded') {
            $discountedPrice = $vehiclePrice - $discount;
            $consumptionTax  = (int) round($discountedPrice * TaxConstants::CONSUMPTION_TAX_RATE);
        } else {
            $discountedPrice = $vehiclePrice;
            $consumptionTax  = (int) round($vehiclePrice * TaxConstants::CONSUMPTION_TAX_RATE) - $discount;
        }

        $accessoriesTotal = collect($estimate->accessories ?? [])
            ->sum(fn($a) => (int)($a['price'] ?? 0));

        $miscTotal = $estimate->misc_fees_total
            + (int) ($estimate->environmental_performance_tax ?? 0)
            + (int) ($estimate->inspection_registration_fee ?? 0)
            + (int) ($estimate->inspection_registration_fee_exempt ?? 0)
            + (int) ($estimate->trade_in_handling_fee ?? 0)
            + (int) ($estimate->assessment_fee ?? 0);

        $totalPrice = $discountedPrice + $consumptionTax + $miscTotal;

        return compact(
            'vehiclePrice',
            'discount',
            'discountedPrice',
            'consumptionTax',
            'miscTotal',
            'totalPrice',
            'accessoriesTotal',
        );
    }

    /**
     * 課税対象額・非課税対象額を計算する
     */
    private function calcTaxAmounts(StkEstimate $estimate, int $discountedPrice, int $accessoriesTotal): array
    {
        $taxableAmount = $discountedPrice
            + $accessoriesTotal
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

        return compact('taxableAmount', 'nonTaxableAmount');
    }

    /**
     * 必要書類を左右2列に分割する
     */
    private function splitDocuments(StkEstimate $estimate): array
    {
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

        $half = (int) ceil(count($documents) / 2);

        return [
            array_slice($documents, 0, $half),
            array_slice($documents, $half),
        ];
    }

    /**
     * 税金/保険料合計を計算する
     */
    private function calcTaxInsuranceTotal(StkEstimate $estimate): int
    {
        return (int) ($estimate->vehicle_tax ?? 0)
            + (int) ($estimate->environmental_performance_tax ?? 0)
            + (int) ($estimate->weight_tax ?? 0)
            + (int) ($estimate->liability_insurance ?? 0);
    }

    /**
     * 課税対象小計を計算する
     */
    private function calcTaxableSubTotal(StkEstimate $estimate): int
    {
        return (int) ($estimate->inspection_registration_fee ?? 0)
            + (int) ($estimate->garage_cert_fee ?? 0)
            + (int) ($estimate->trade_in_handling_fee ?? 0)
            + (int) ($estimate->delivery_fee ?? 0)
            + (int) ($estimate->assessment_fee ?? 0)
            + (int) ($estimate->maintenance_fee ?? 0);
    }

    /**
     * 非課税小計を計算する
     */
    private function calcNonTaxableSubTotal(StkEstimate $estimate): int
    {
        return (int) ($estimate->inspection_registration_fee_exempt ?? 0)
            + (int) ($estimate->registration_fee ?? 0);
    }

    /**
     * 修復歴ラベルを返す
     */
    private function resolveRepairHistoryLabel(?string $repairHistory): string
    {
        return RepairHistory::label($repairHistory ?? '');
    }

    /**
     * ミッションラベルを返す
     */
    private function resolveTransmissionLabel(?string $transmission): string
    {
        return Transmission::LABELS[$transmission] ?? '-';
    }

    /**
     * 記録簿ラベルを返す
     */
    private function resolveServiceRecordLabel(?bool $hasServiceRecord): string
    {
        return match($hasServiceRecord) {
            true    => '● 有 ○ 無',
            false   => '○ 有 ● 無',
            default => '-',
        };
    }

    /**
     * 自賠責の残月数を返す
     */
    private function calcLiabilityInsuranceMonths(?object $detail): string
    {
        if (!$detail?->inspection_expire_date) {
            return '-';
        }

        $expireDate = \Carbon\Carbon::parse($detail->inspection_expire_date);
        $months     = (int) now()->diffInMonths($expireDate, false);

        return $months > 0 ? $months . 'ヶ月' : '-';
    }

    /**
     * 排気量をフォーマットする
     */
    private function formatDisplacement(?int $displacement): string
    {
        return $displacement ? number_format($displacement) . 'cc' : '-';
    }

    /**
     * 走行距離をフォーマットする
     */
    private function formatMileage(?int $mileage): string
    {
        return $mileage !== null ? number_format($mileage) . 'km' : '-';
    }

    /**
     * クレジット月数をフォーマットする
     */
    private function formatCreditMonths(StkEstimate $estimate): string
    {
        if (!$estimate->credit_months) {
            return '-';
        }
        return $estimate->credit_months . '回　分割手数料　' . number_format($estimate->credit_fee ?? 0) . '円';
    }

    /**
     * 月払をフォーマットする
     */
    private function formatMonthlyPayment(StkEstimate $estimate): string
    {
        if (!$estimate->monthly_payment) {
            return '初回　-';
        }
        return '初回　' . number_format($estimate->monthly_payment) . '円';
    }

    /**
     * 賞与払をフォーマットする
     */
    private function formatBonusPayment(StkEstimate $estimate): string
    {
        if (!$estimate->bonus_payment) {
            return '賞与　月　-';
        }
        return '賞与　月　' . number_format($estimate->bonus_payment) . '円';
    }

    /**
     * 郵便番号をフォーマットする
     * 例：0000000 → 000-0000
     */
    private function formatPostalCode(?string $postalCode): ?string
    {
        if (!$postalCode) {
            return null;
        }

        $raw = preg_replace('/[^0-9]/', '', $postalCode);

        return strlen($raw) === self::POSTAL_CODE_LENGTH
            ? substr($raw, 0, self::POSTAL_CODE_HYPHEN_POSITION) . '-' . substr($raw, self::POSTAL_CODE_HYPHEN_POSITION)
            : $postalCode;
    }

    /**
     * PDFを生成する（表面のみ）
     */
    protected function generate(StkEstimate $estimate, string $documentType)
    {
        $estimate->load(['car.series', 'car.detail', 'car.dealerFee', 'dealer', 'createdBy']);

        $html = $this->buildFrontHtml($estimate, $documentType);

        return $this->buildPdf($html);
    }

    /**
     * PDFを生成する（表面＋裏面）
     */
    protected function generateWithBack(StkEstimate $estimate, string $documentType)
    {
        $estimate->load(['car.series', 'car.detail', 'car.dealerFee', 'dealer', 'createdBy']);

        $frontHtml = $this->buildFrontHtml($estimate, $documentType);
        $backHtml  = view('pdf.contract_back')->render();

        // 裏面全体をpage-breakクラスのdivで囲む
        $html = $frontHtml
            . '<div class="page-break">'
            . $backHtml
            . '</div>';

        return $this->buildPdf($html);
    }

    /**
     * 表面HTMLを生成する
     */
    private function buildFrontHtml(StkEstimate $estimate, string $documentType): string
    {
        $car    = $estimate->car;
        $detail = $car->detail;

        $prices     = $this->calcPrices($estimate);
        $taxAmounts = $this->calcTaxAmounts($estimate, $prices['discountedPrice'], $prices['accessoriesTotal']);
        [$docsLeft, $docsRight] = $this->splitDocuments($estimate);

        return view('pdf.estimate', [
            'estimate'                 => $estimate,
            'car'                      => $car,
            'series'                   => $car->series,
            'detail'                   => $detail,
            'maker'                    => $car->manufacturer,
            'dealer'                   => $estimate->dealer,
            'modelYear'                => new JapaneseEra($car->model_year),
            'transmissionLabel'        => $this->resolveTransmissionLabel($car->transmission),
            'repairHistoryLabel'       => $this->resolveRepairHistoryLabel($car->repair_history),
            'serviceRecordLabel'       => $this->resolveServiceRecordLabel($estimate->has_service_record),
            'taxInsuranceTotal'        => $this->calcTaxInsuranceTotal($estimate),
            'taxableSubTotal'          => $this->calcTaxableSubTotal($estimate),
            'nonTaxableSubTotal'       => $this->calcNonTaxableSubTotal($estimate),
            'displacementLabel'        => $this->formatDisplacement($detail?->displacement),
            'mileageLabel'             => $this->formatMileage($car->mileage),
            'creditMonthsLabel'        => $this->formatCreditMonths($estimate),
            'monthlyPaymentLabel'      => $this->formatMonthlyPayment($estimate),
            'bonusPaymentLabel'        => $this->formatBonusPayment($estimate),
            'dealerPostalCode'         => $this->formatPostalCode($estimate->dealer?->postal_code),
            'liabilityInsuranceMonths' => $this->calcLiabilityInsuranceMonths($detail),
            'postalCode'               => $this->formatPostalCode($estimate->customer_postal_code),
            'taxRateDisplay'           => TaxConstants::CONSUMPTION_TAX_RATE_DISPLAY,
            'documentsLeft'            => $docsLeft,
            'documentsRight'           => $docsRight,
            // 価格
            'vehiclePrice'             => $prices['vehiclePrice'],
            'discount'                 => $prices['discount'],
            'discountedPrice'          => $prices['discountedPrice'],
            'consumptionTax'           => $prices['consumptionTax'],
            'miscTotal'                => $prices['miscTotal'],
            'totalPrice'               => $prices['totalPrice'],
            'accessoriesTotal'         => $prices['accessoriesTotal'],
            // 税額
            'taxableAmount'            => $taxAmounts['taxableAmount'],
            'nonTaxableAmount'         => $taxAmounts['nonTaxableAmount'],
            // ドキュメント種別
            ...$this->buildDocumentMeta($documentType),
        ])->render();
    }

    /**
     * PDFオプションを設定して生成する
     */
    private function buildPdf(string $html)
    {
        $wrappedHtml = '<!DOCTYPE html><html lang="ja"><head><meta charset="UTF-8"></head><body>'
            . $html
            . '</body></html>';

        return Pdf::setOptions([
            'defaultFont'            => self::PDF_FONT_JAPANESE,
            'fontDir'                => base_path(self::PDF_FONT_DIR),
            'fontCache'              => base_path(self::PDF_FONT_DIR),
            'enable_php'             => true,
            'isRemoteEnabled'        => true,
            'enable_font_subsetting' => true,
            'dpi'                    => self::PDF_DPI,
        ])->loadHTML($wrappedHtml, 'UTF-8')->setPaper('a4', 'portrait');
    }
}