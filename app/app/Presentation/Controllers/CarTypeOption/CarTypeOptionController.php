<?php

namespace App\Presentation\Controllers\CarTypeOption;

use App\Application\UseCases\CarTypeOption\CarTypeOptionUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CarTypeOptionController extends Controller
{
    public function __construct(
        private readonly CarTypeOptionUseCase $useCase,
    ) {}

    public function index(): JsonResponse
    {
        $output = $this->useCase->handle();

        return response()->json($output->toArray());
    }
}