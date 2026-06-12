<?php

declare(strict_types=1);

namespace App\Application\Services\Pdf;

use App\Domain\Shared\Enums\PdfDocumentType;
use App\Infrastructure\Eloquent\User\StkEstimate;

/**
 * 契約書PDF生成サービス
 *
 * 表面：見積書と同じレイアウト（タイトル・番号・署名欄等を切り替え）
 * 裏面：約款（contract_back.blade.php）
 */
class ContractPdfService extends BasePdfService
{
    /**
     * 契約書PDFを生成してダウンロードレスポンスを返す
     * 表面＋裏面の2ページで出力する
     */
    public function download(StkEstimate $estimate)
    {
        $filename = "契約書_{$estimate->estimate_number}.pdf";

        return $this->generateWithBack($estimate, PdfDocumentType::CONTRACT)->download($filename);
    }

    public function generateContent(StkEstimate $estimate): string
    {
        return $this->generateWithBack($estimate, PdfDocumentType::CONTRACT)->output();
    }
}