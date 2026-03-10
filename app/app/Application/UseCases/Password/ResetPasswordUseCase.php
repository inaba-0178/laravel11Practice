<?php

namespace App\Application\UseCases\Password;

use App\Domain\Password\Repositories\PasswordResetTokenRepositoryInterface;
use App\Domain\Password\ValueObjects\ResetPasswordRequest;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Domain\Shared\Constants\PasswordPolicy;

class ResetPasswordUseCase
{
    public function __construct(
        private readonly PasswordResetTokenRepositoryInterface $passwordResetTokenRepository,
        private readonly UserRepositoryInterface               $userRepository,
    ) {}

    public function execute(ResetPasswordRequest $request): void
    {
        $record = $this->passwordResetTokenRepository->findByEmail($request->email);

        if (!$record) {
            throw new \RuntimeException('無効なトークンです');
        }

        if (!hash_equals($record->token, hash(PasswordPolicy::HASH_ALGORITHM, $request->token))) {
            throw new \RuntimeException('無効なトークンです');
        }

        if (now()->gt(Carbon::parse($record->created_at)->addMinutes(PasswordPolicy::RESET_TOKEN_EXPIRE_MINUTES))) {
            throw new \RuntimeException('トークンの有効期限が切れています');
        }

        $this->userRepository->updatePassword($request->email, Hash::make($request->password));

        $this->passwordResetTokenRepository->deleteByEmail($request->email);
    }
}