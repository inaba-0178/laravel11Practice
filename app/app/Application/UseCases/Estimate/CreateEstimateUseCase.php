<?php

declare(strict_types=1);

namespace App\Application\UseCases\Estimate;

use App\Infrastructure\Eloquent\User\StkEstimate;
use App\Infrastructure\Repositories\Estimate\EloquentEstimateRepository;
use App\Application\Services\Pdf\EstimatePdfService;

/**
 * 見積作成ユースケース
 *
 * 見積データの保存・PDF生成・S3アップロードを担当する。
 */
class CreateEstimateUseCase
{
    public function __construct(
        private readonly EloquentEstimateRepository $repository,
        private readonly EstimatePdfService         $pdfService,
    ) {}

    /**
     * 見積を作成する
     *
     * 処理の流れ：
     * 1. 見積番号を自動採番
     * 2. 見積データをDBに保存
     * 3. PDFを生成してS3にアップロード
     * 4. PDF S3パスをDBに保存
     *
     * @param CreateEstimateInputData $data
     * @return CreateEstimateOutputData
     */
    public function execute(CreateEstimateInputData $data): CreateEstimateOutputData
    {
        // 見積番号を自動採番
        $estimateNumber = StkEstimate::generateEstimateNumber();

        // 見積データをDBに保存
        $estimate = $this->repository->create($data, $estimateNumber);

        // PDFを生成してS3にアップロード
        $pdfPath = $this->pdfService->generateAndUpload($estimate);

        // PDF S3パスをDBに更新
        $estimate->update(['pdf_path' => $pdfPath]);

        return new CreateEstimateOutputData(
            estimate: $estimate,
            pdfPath:  $pdfPath,
        );
    }
}