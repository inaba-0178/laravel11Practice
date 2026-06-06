<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Estimate;

use App\Application\Services\EstimatePdfService;
use App\Infrastructure\Eloquent\User\StkEstimate;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * 見積PDFダウンロードコントローラー
 *
 * GET /estimate/{estimate}/download
 */
class EstimateDownloadController extends Controller
{
    public function __construct(
        private readonly EstimatePdfService $pdfService,
    ) {}

    public function __invoke(StkEstimate $estimate): Response
    {
        // アクセス権限チェック
        $user = Auth::user();
        if (
            !in_array($user?->role, ['super', 'admin']) &&
            $user?->dealer_id !== $estimate->dealer_id
        ) {
            abort(403);
        }

        return $this->pdfService->download($estimate);
    }
}