<?php

namespace App\Presentation\Controllers\EquipmentDressup;

use App\Application\UseCases\EquipmentDressup\EquipmentDressupUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class EquipmentDressupController extends Controller
{
    public function __construct(
        private readonly EquipmentDressupUseCase $useCase,
    ) {}

    public function index(): JsonResponse
    {
        $output = $this->useCase->handle();

        return response()->json($output->toArray());
    }
}