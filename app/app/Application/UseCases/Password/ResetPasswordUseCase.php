<?php

namespace App\Application\UseCases\Password;

use App\Domain\Password\Repositories\PasswordResetTokenRepositoryInterface;
use App\Domain\Password\ValueObjects\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordUseCase
{
    public function __construct(
        private readonly PasswordResetTokenRepositoryInterface $passwordResetTokenRepository,
    ) {}

    public function execute(ResetPasswordRequest $request): void
    {
        $record = $this->passwordResetTokenRepository->findByEmail($request->email);

        if (!$record) {
            throw new \RuntimeException('無効なトークンです');
        }

        if (!hash_equals($record->token, hash('sha256', $request->token))) {
            throw new \RuntimeException('無効なトークンです');
        }

        if (now()->diffInMinutes($record->created_at) > 30) {
            throw new \RuntimeException('トークンの有効期限が切れています');
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        $this->passwordResetTokenRepository->deleteByEmail($request->email);
    }
}