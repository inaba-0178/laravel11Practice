<?php

namespace App\Application\UseCases\MemberAuth;

use App\Domain\Auth\ValueObjects\LoginCredentials;
use App\Infrastructure\Eloquent\User\Member;
use Illuminate\Support\Facades\Hash;

class MemberLoginUseCase
{
    public function execute(LoginCredentials $credentials): MemberLoginOutputData
    {
        $member = Member::where('email', $credentials->email)->first();

        if (!$member || !Hash::check($credentials->password, $member->password)) {
            throw new \RuntimeException('メールアドレスまたはパスワードが正しくありません');
        }

        $token = $member->createToken('member-token')->plainTextToken;

        return new MemberLoginOutputData(member: $member, token: $token);
    }
}