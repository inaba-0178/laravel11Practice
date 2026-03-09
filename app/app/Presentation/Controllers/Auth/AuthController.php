<?php

namespace App\Presentation\Controllers\Auth;

use App\Application\UseCases\Auth\LoginUseCase;
use App\Application\UseCases\Auth\LogoutUseCase;
use App\Domain\Auth\ValueObjects\LoginCredentials;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly LoginUseCase   $loginUseCase,
        private readonly LogoutUseCase  $logoutUseCase,
    ) {}

    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = new LoginCredentials(
                email:    $request->email,
                password: $request->password,
            );

            $result = $this->loginUseCase->execute($credentials);

            return response()->json($result->toArray());

        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->logoutUseCase->execute($request->user());

        return response()->json(['status' => 'ok']);
    }
}