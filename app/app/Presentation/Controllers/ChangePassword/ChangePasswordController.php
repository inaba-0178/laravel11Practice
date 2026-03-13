<?php

namespace App\Presentation\Controllers\ChangePassword;

use App\Application\UseCases\ChangePassword\ChangePasswordUseCase;
use App\Domain\ChangePassword\ValueObjects\ChangePasswordRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChangePasswordController extends Controller
{
    public function __construct(
        private readonly ChangePasswordUseCase $changePasswordUseCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'current_password'      => 'required|string',
            'new_password'          => 'required|string',
            'new_password_confirmation' => 'required|string',
        ]);

        try {
            $changePasswordRequest = new ChangePasswordRequest(
                currentPassword:          $request->current_password,
                newPassword:              $request->new_password,
                newPasswordConfirmation:  $request->new_password_confirmation,
            );

            $this->changePasswordUseCase->execute(
                $request->user()->id,
                $changePasswordRequest,
            );

            return response()->json(['message' => 'パスワードを変更しました']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}