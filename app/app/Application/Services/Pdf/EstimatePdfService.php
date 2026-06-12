<?php

declare(strict_types=1);

namespace App\Application\Services\Pdf;

use App\Domain\Shared\Enums\PdfDocumentType;
use App\Infrastructure\Eloquent\Opr\OprSetting;
use App\Infrastructure\Eloquent\User\StkEstimate;
use Illuminate\Support\Facades\Storage;

/**
 * 見積PDF生成サービス
 *
 * opr_settingsの estimate_pdf_upload_s3 で
 * S3アップロードのON/OFFを切り替え可能。
 */
class EstimatePdfService extends BasePdfService
{
    /**
     * PDFを生成してS3にアップロードする
     */
    public function generateAndUpload(StkEstimate $estimate): ?string
    {
        $pdf = $this->generate($estimate, PdfDocumentType::ESTIMATE);

        if (!$this->isS3Enabled()) {
            return null;
        }

        $path = $this->buildS3Path($estimate);
        Storage::disk('s3')->put($path, $pdf->output());

        return $path;
    }

    /**
     * PDFを生成してダウンロードレスポンスを返す
     */
    public function download(StkEstimate $estimate)
    {
        $filename = "見積書_{$estimate->estimate_number}.pdf";

        return $this->generate($estimate, PdfDocumentType::ESTIMATE)->download($filename);
    }

    /**
     * PDFのバイナリデータを返す（メール添付用）
     */
    public function generateContent(StkEstimate $estimate): string
    {
        return $this->generate($estimate, PdfDocumentType::ESTIMATE)->output();
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