<?php

namespace App\Presentation\Controllers\LoanMonthlyOption;

use App\Application\UseCases\LoanMonthlyOption\LoanMonthlyOptionUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class LoanMonthlyOptionController extends Controller
{
    public function __construct(
        private readonly LoanMonthlyOptionUseCase $useCase,
    ) {}

    public function index(): JsonResponse
    {
        $output = $this->useCase->handle();

        return response()->json($output->toArray());
    }
}