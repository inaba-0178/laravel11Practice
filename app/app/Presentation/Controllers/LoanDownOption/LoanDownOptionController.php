<?php

namespace App\Presentation\Controllers\LoanDownOption;

use App\Application\UseCases\LoanDownOption\LoanDownOptionUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class LoanDownOptionController extends Controller
{
    public function __construct(
        private readonly LoanDownOptionUseCase $useCase,
    ) {}

    public function index(): JsonResponse
    {
        $output = $this->useCase->handle();

        return response()->json($output->toArray());
    }
}