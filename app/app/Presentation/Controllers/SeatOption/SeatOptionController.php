<?php

namespace App\Presentation\Controllers\SeatOption;

use App\Application\UseCases\SeatOption\SeatOptionUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SeatOptionController extends Controller
{
    public function __construct(
        private readonly SeatOptionUseCase $useCase,
    ) {}

    public function index(): JsonResponse
    {
        $output = $this->useCase->handle();

        return response()->json($output->toArray());
    }
}