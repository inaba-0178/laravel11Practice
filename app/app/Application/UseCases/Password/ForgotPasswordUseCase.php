<?php

namespace App\Application\UseCases\Password;

use App\Domain\MemberPassword\Repositories\MemberPasswordRepositoryInterface;
use App\Domain\Password\Repositories\PasswordResetTokenRepositoryInterface;
use App\Domain\Password\ValueObjects\ForgotPasswordEmail;
use Illuminate\Support\Str;
use App\Domain\Shared\Constants\PasswordPolicy;
use App\Application\Services\MailService;
use App\Domain\Shared\Constants\MailTemplateId;

class ForgotPasswordUseCase
{
    public function __construct(
        private readonly MemberPasswordRepositoryInterface     $memberPasswordRepository,
        private readonly PasswordResetTokenRepositoryInterface $passwordResetTokenRepository,
        private readonly MailService                           $mailService,
    ) {}

    public function execute(ForgotPasswordEmail $email): void
    {
        $user = $this->memberPasswordRepository->findByEmail($email->email);

        if (!$user) {
            return;
        }

        $token = Str::random(PasswordPolicy::TOKEN_LENGTH);

        $this->passwordResetTokenRepository->upsert($email->email, $token);

        $this->mailService->send(
            templateId:   MailTemplateId::PASSWORD_RESET,
            toEmail:      $email->email,
            placeholders: [
                'url'   => config('app.frontend_url') . '/reset-password?token=' . $token . '&email=' . urlencode($email->email),
                'token' => $token,
                'email' => $email->email,
            ],
        );
    }
}