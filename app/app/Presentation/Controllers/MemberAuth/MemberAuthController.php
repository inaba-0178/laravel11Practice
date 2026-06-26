<?php

namespace App\Presentation\Controllers\MemberAuth;

use App\Application\Services\AuditLogger;
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

            $member = $result->getMember();
            AuditLogger::logAuth('login', (int) $member->id, $member->sei . $member->mei, 'member', 'success');

            $cookieMinutes = (int) config('sanctum.expiration', 10080);

            return response()->json($result->toArray())
                ->cookie('member_token', $result->getToken(), $cookieMinutes, '/', null, false, true);

        } catch (\RuntimeException $e) {
            AuditLogger::logAuth('login', null, $request->email ?? '', 'member', 'failed');
            return response()->json(['message' => $e->getMessage()], 401);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user('member');
        AuditLogger::logAuth('logout', $user ? (int) $user->id : null, $user ? ($user->sei . $user->mei) : null, 'member', 'success');

        $this->memberLogoutUseCase->execute($user);

        return response()->json(['status' => 'ok'])
            ->withoutCookie('member_token');
    }
}