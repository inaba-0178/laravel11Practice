<?php

namespace App\Application\UseCases\MemberAuth;

use App\Domain\Auth\ValueObjects\LoginCredentials;
use App\Domain\MemberAuth\Repositories\MemberAuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class MemberLoginUseCase
{
    public function __construct(
        private readonly MemberAuthRepositoryInterface $memberAuthRepository,
    ) {}

    public function execute(LoginCredentials $credentials): MemberLoginOutputData
    {
        $member = $this->memberAuthRepository->findByEmail($credentials->email);

        if (!$member || !Hash::check($credentials->password, $member->password)) {
            throw new \RuntimeException('メールアドレスまたはパスワードが正しくありません');
        }

        $token = $member->createToken('member-token')->plainTextToken;

        return new MemberLoginOutputData(member: $member, token: $token);
    }
}