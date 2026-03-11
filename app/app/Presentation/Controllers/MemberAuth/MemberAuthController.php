<?php

namespace App\Presentation\Controllers\MemberAuth;

use App\Application\UseCases\MemberAuth\MemberLoginUseCase;
use App\Application\UseCases\MemberAuth\MemberLogoutUseCase;
use App\Domain\Auth\ValueObjects\LoginCredentials;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberAuthController extends Controller
{
    public function __construct(
        private readonly MemberLoginUseCase  $memberLoginUseCase,
        private readonly MemberLogoutUseCase $memberLogoutUseCase,
    ) {}

    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = new LoginCredentials(
                email:    $request->email ?? '',
                password: $request->password ?? '',
            );

            $result = $this->memberLoginUseCase->execute($credentials);

            return response()->json($result->toArray());

        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->memberLogoutUseCase->execute($request->user('member'));

        return response()->json(['status' => 'ok']);
    }
}