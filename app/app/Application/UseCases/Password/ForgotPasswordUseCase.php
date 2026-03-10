<?php

namespace App\Application\UseCases\Password;

use App\Domain\Password\Repositories\PasswordResetTokenRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Password\ValueObjects\ForgotPasswordEmail;
use App\Infrastructure\Notifications\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface               $userRepository,
        private readonly PasswordResetTokenRepositoryInterface $passwordResetTokenRepository,
    ) {}

    public function execute(ForgotPasswordEmail $email): void
    {
        $user = $this->userRepository->findByEmail($email->email);

        if (!$user) {
            return;
        }

        $token = Str::random(64);

        $this->passwordResetTokenRepository->upsert($email->email, $token);

        Mail::to($email->email)->send(new PasswordResetMail($token, $email->email));
    }
}