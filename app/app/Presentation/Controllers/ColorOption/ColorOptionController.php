<?php

namespace App\Presentation\Controllers\ColorOption;

use App\Application\UseCases\ColorOption\ColorOptionsUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ColorOptionController extends Controller
{
    public function __construct(
        private readonly ColorOptionsUseCase $useCase,
    ) {}

    public function index(): JsonResponse
    {
        $output = $this->useCase->handle();

        return response()->json($output->toArray());
    }
}