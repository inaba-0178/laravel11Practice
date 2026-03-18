<?php

namespace App\Presentation\Controllers\DetailOption;

use App\Application\UseCases\DetailOption\DetailOptionsUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DetailOptionController extends Controller
{
    public function __construct(
        private readonly DetailOptionsUseCase $useCase,
    ) {}

    public function index(): JsonResponse
    {
        $output = $this->useCase->handle();

        return response()->json($output->toArray());
    }
}