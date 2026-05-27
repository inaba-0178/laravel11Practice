<?php

namespace App\Presentation\Controllers\OprMainViewLists;

use App\Application\UseCases\OprMainViewLists\OprMainViewsUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class OprMainViewController extends Controller
{
    public function __construct(
        private readonly OprMainViewsUseCase $useCase
    ) {}

    public function index(): JsonResponse
    {
        $result = $this->useCase->execute();
        return response()->json($result->toArray());
    }
}