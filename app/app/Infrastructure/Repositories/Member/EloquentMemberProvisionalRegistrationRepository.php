<?php

namespace App\Infrastructure\Repositories\Member;

use App\Domain\Member\Entities\ProvisionalRegistration;
use App\Domain\Member\Repositories\MemberProvisionalRegistrationRepositoryInterface;
use App\Domain\Shared\Constants\PasswordPolicy;
use App\Infrastructure\Eloquent\User\ProvisionalRegistration as EloquentProvisionalRegistration;

class EloquentMemberProvisionalRegistrationRepository implements MemberProvisionalRegistrationRepositoryInterface
{
    public function upsert(string $email, string $token): void
    {
        EloquentProvisionalRegistration::updateOrCreate(
            ['email' => $email],
            [
                'token'      => hash(PasswordPolicy::HASH_ALGORITHM, $token),
                'created_at' => now(),
            ]
        );
    }

    public function findByEmail(string $email): ?ProvisionalRegistration
    {
        $record = EloquentProvisionalRegistration::where('email', $email)->first();

        if (!$record) {
            return null;
        }

        return new ProvisionalRegistration(
            email:      $record->email,
            token:      $record->token,
            created_at: $record->created_at,
        );
    }

    public function deleteByEmail(string $email): void
    {
        EloquentProvisionalRegistration::where('email', $email)->delete();
    }
}