<?php

namespace App\Presentation\Controllers\Password;

use App\Application\UseCases\Password\ForgotPasswordUseCase;
use App\Application\UseCases\Password\ResetPasswordUseCase;
use App\Domain\Password\ValueObjects\ForgotPasswordEmail;
use App\Domain\Password\ValueObjects\ResetPasswordRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    public function __construct(
        private readonly ForgotPasswordUseCase $forgotPasswordUseCase,
        private readonly ResetPasswordUseCase  $resetPasswordUseCase,
    ) {}

    public function forgot(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $email = new ForgotPasswordEmail(email: $request->email);
            $this->forgotPasswordUseCase->execute($email);
            return response()->json(['message' => 'パスワードリセットメールを送信しました']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'エラーが発生しました'], 500);
        }
    }

    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token'                 => 'required|string',
            'email'                 => 'required|email',
            'password'              => 'required|string|confirmed',
        ]);

        try {
            $resetRequest = new ResetPasswordRequest(
                token:    $request->token,
                email:    $request->email,
                password: $request->password,
            );
            $this->resetPasswordUseCase->execute($resetRequest);
            return response()->json(['message' => 'パスワードを変更しました']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}