<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }

    protected function authenticate($request, array $guards): void
    {
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                $this->auth->shouldUse($guard);
                return;
            }
        }

        if (in_array('members', $guards) && !$request->bearerToken()) {
            if ($this->tryAuthenticateFromCookie($request)) {
                $this->auth->shouldUse('members');
                return;
            }
        }

        $this->unauthenticated($request, $guards);
    }

    private function tryAuthenticateFromCookie(Request $request): bool
    {
        $cookieValue = $request->cookie('member_token');
        if (!$cookieValue) {
            return false;
        }

        $token = PersonalAccessToken::findToken($cookieValue);
        if (!$token || !$token->tokenable) {
            return false;
        }

        if ($token->expires_at && now()->gte($token->expires_at)) {
            return false;
        }

        $expiration = config('sanctum.expiration');
        if ($expiration && $token->created_at->lte(now()->subMinutes($expiration))) {
            return false;
        }

        $user = $token->tokenable->withAccessToken($token);
        $this->auth->guard('members')->setUser($user);

        return true;
    }
}
