<?php

namespace App\Presentation\Controllers\EquipmentBasic;

use App\Application\UseCases\EquipmentBasic\EquipmentBasicUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class EquipmentBasicController extends Controller
{
    public function __construct(
        private readonly EquipmentBasicUseCase $useCase,
    ) {}

    public function index(): JsonResponse
    {
        $output = $this->useCase->handle();

        return response()->json($output->toArray());
    }
}