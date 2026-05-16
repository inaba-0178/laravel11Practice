<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Maintenance;

use App\Infrastructure\Eloquent\Opr\OprMaintenance;
use Illuminate\Http\JsonResponse;

class MaintenanceController
{
    public function status(): JsonResponse
    {
        $maintenance = OprMaintenance::getInstance();

        return response()->json([
            'is_maintenance'   => (bool) $maintenance->is_maintenance,
            'message'          => $maintenance->message,
            'started_at'       => $maintenance->started_at?->format('Y/m/d H:i'),
            'estimated_end_at' => $maintenance->estimated_end_at?->format('Y/m/d H:i'),
        ]);
    }
}