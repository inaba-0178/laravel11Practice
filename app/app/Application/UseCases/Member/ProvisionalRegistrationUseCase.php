<?php

namespace App\Application\UseCases\Member;

use App\Domain\Member\Repositories\MemberProvisionalRegistrationRepositoryInterface;
use App\Domain\Member\Repositories\MemberRepositoryInterface;
use App\Domain\Member\ValueObjects\RegisterEmail;
use Illuminate\Support\Str;
use App\Domain\Shared\Constants\PasswordPolicy;
use App\Application\Services\MailService;
use App\Domain\Shared\Constants\MailTemplateId;

class ProvisionalRegistrationUseCase
{
    public function __construct(
        private readonly MemberRepositoryInterface                          $memberRepository,
        private readonly MemberProvisionalRegistrationRepositoryInterface   $provisionalRegistrationRepository,
        private readonly MailService                                        $mailService,
    ) {}

    public function execute(RegisterEmail $email): void
    {
        // 既に本登録済みのメールアドレスチェック
        $existingMember = $this->memberRepository->findByEmail($email->email);
        if ($existingMember) {
            throw new \RuntimeException('このメールアドレスは既に登録されています');
        }

        $token = Str::random(PasswordPolicy::TOKEN_LENGTH);

        $this->provisionalRegistrationRepository->upsert($email->email, $token);

        $this->mailService->send(
            templateId:   MailTemplateId::PROVISIONAL_REGISTRATION,
            toEmail:      $email->email,
            placeholders: [
                'url'   => config('app.frontend_url') . '/register-form?token=' . $token . '&email=' . urlencode($email->email),
                'token' => $token,
                'email' => $email->email,
            ],
        );
    }
}