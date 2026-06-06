<?php

declare(strict_types=1);

namespace App\Application\UseCases\Estimate;

use App\Infrastructure\Eloquent\User\StkEstimate;

/**
 * 見積作成ユースケースの出力データ
 */
final class CreateEstimateOutputData
{
    public function __construct(
        /** 作成された見積 */
        public readonly StkEstimate $estimate,

        /** PDF S3パス */
        public readonly string $pdfPath,
    ) {}
}