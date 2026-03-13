<?php

namespace App\Application\UseCases\Password;

use App\Domain\MemberPassword\Repositories\MemberPasswordRepositoryInterface;
use App\Domain\Password\Repositories\PasswordResetTokenRepositoryInterface;
use App\Domain\Password\ValueObjects\ForgotPasswordEmail;
use App\Infrastructure\Notifications\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Domain\Shared\Constants\PasswordPolicy;

class ForgotPasswordUseCase
{
    public function __construct(
        private readonly MemberPasswordRepositoryInterface     $memberPasswordRepository,
        private readonly PasswordResetTokenRepositoryInterface $passwordResetTokenRepository,
    ) {}

    public function execute(ForgotPasswordEmail $email): void
    {
        $user = $this->memberPasswordRepository->findByEmail($email->email);

        if (!$user) {
            return;
        }

        $token = Str::random(PasswordPolicy::TOKEN_LENGTH);

        $this->passwordResetTokenRepository->upsert($email->email, $token);

        Mail::to($email->email)->send(new PasswordResetMail($token, $email->email));
    }
}