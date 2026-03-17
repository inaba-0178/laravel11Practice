<?php

namespace App\Presentation\Controllers\BasicOption;

use App\Application\UseCases\BasicOption\BasicOptionsUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BasicOptionController extends Controller
{
    public function __construct(
        private readonly BasicOptionsUseCase $useCase,
    ) {}

    public function index(): JsonResponse
    {
        $output = $this->useCase->handle();

        return response()->json($output->toArray());
    }
}