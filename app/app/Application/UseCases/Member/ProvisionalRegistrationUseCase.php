<?php

namespace App\Application\UseCases\Member;

use App\Domain\Member\Repositories\MemberProvisionalRegistrationRepositoryInterface;
use App\Domain\Member\Repositories\MemberRepositoryInterface;
use App\Domain\Member\ValueObjects\RegisterEmail;
use App\Infrastructure\Notifications\Mail\ProvisionalRegistrationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Domain\Shared\Constants\PasswordPolicy;

class ProvisionalRegistrationUseCase
{
    public function __construct(
        private readonly MemberRepositoryInterface                    $memberRepository,
        private readonly MemberProvisionalRegistrationRepositoryInterface $provisionalRegistrationRepository,
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

        Mail::to($email->email)->send(new ProvisionalRegistrationMail($token, $email->email));
    }
}