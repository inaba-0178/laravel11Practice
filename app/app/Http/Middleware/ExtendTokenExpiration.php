<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ExtendTokenExpiration
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken && $token->expires_at !== null) {
            $token->forceFill([
                'expires_at' => now()->addMinutes((int) config('sanctum.expiration')),
            ])->save();
        }

        return $response;
    }
}
