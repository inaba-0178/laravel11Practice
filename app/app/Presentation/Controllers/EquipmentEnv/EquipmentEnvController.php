<?php

namespace App\Presentation\Controllers\EquipmentEnv;

use App\Application\UseCases\EquipmentEnv\EquipmentEnvUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class EquipmentEnvController extends Controller
{
    public function __construct(
        private readonly EquipmentEnvUseCase $useCase,
    ) {}

    public function index(): JsonResponse
    {
        $output = $this->useCase->handle();

        return response()->json($output->toArray());
    }
}