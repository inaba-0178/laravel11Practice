<?php

namespace App\Filament\Widgets;

use App\Infrastructure\Eloquent\User\StkBulkUploadBatch;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Constants\CarStatus;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Constants\DashboardLayout;

class DealerBulkCarStatusWidget extends Widget
{
    protected static string         $view       = 'filament.widgets.dealer-bulk-car-status-widget';
    protected static ?int           $sort       = DashboardLayout::SORT_BULK_CAR_STATUS;
    protected int | string | array  $columnSpan = DashboardLayout::SPAN_BULK_CAR_STATUS;

    // 最大表示件数（変更する場合はここを修正）
    const MAX_DISPLAY = 100;

    public static function canView(): bool
    {
        return Auth::user()?->dealer_id !== null;
    }

    public function getSummary(): array
    {
        $dealerId = Auth::user()->dealer_id;

        $batches = StkBulkUploadBatch::where('dealer_id', $dealerId)->get();

        return [
            'total'    => $batches->count(),
            'pending'  => $batches->filter(fn($b) => $b->pending_count > 0)->count(),
            'rejected' => $batches->filter(fn($b) => $b->rejected_count > 0)->count(),
        ];
    }

    public function getBatches(): array
    {
        $dealerId = Auth::user()->dealer_id;

        return StkBulkUploadBatch::where('dealer_id', $dealerId)
            ->with('cars')
            ->orderBy('updated_at', 'desc')
            ->limit(self::MAX_DISPLAY)
            ->get()
            ->map(fn($batch) => [
                'id'             => $batch->id,
                'uploadedAt'     => $batch->uploaded_at?->format('Y/m/d H:i'),
                'updatedAt'      => $batch->updated_at?->format('Y/m/d H:i'),
                'totalCount'     => $batch->total_count,
                'pendingCount'   => $batch->pending_count,
                'approvedCount'  => $batch->approved_count,
                'rejectedCount'  => $batch->rejected_count,
                'status'         => $batch->status,
                'statusLabel'    => $batch->status_label,
                'detailUrl'      => route('filament.admin.pages.bulk-car-batch-detail-page') . '?batchId=' . $batch->id,
            ])
            ->toArray();
    }
}