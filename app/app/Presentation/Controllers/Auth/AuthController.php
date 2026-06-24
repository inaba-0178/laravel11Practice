<?php

namespace App\Presentation\Controllers\Auth;

use App\Application\Services\AuditLogger;
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

            AuditLogger::logAuth('login', $result->getUser()->id, $result->getUser()->name, 'admin', 'success');

            return response()->json($result->toArray());

        } catch (\RuntimeException $e) {
            AuditLogger::logAuth('login', null, $request->email, 'admin', 'failed');
            return response()->json(['message' => $e->getMessage()], 401);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        AuditLogger::logAuth('logout', $user?->id, $user?->name ?? $user?->email, 'admin', 'success');

        $this->logoutUseCase->execute($user);

        return response()->json(['status' => 'ok']);
    }
}