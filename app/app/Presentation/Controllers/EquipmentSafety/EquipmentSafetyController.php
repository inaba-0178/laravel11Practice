<?php

namespace App\Presentation\Controllers\EquipmentSafety;

use App\Application\UseCases\EquipmentSafety\EquipmentSafetyUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class EquipmentSafetyController extends Controller
{
    public function __construct(
        private readonly EquipmentSafetyUseCase $useCase,
    ) {}

    public function index(): JsonResponse
    {
        $output = $this->useCase->handle();

        return response()->json($output->toArray());
    }
}