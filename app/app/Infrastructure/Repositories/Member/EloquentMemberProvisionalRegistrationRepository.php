<?php

namespace App\Infrastructure\Repositories\Member;

use App\Domain\Member\Repositories\MemberProvisionalRegistrationRepositoryInterface;
use App\Domain\Shared\Constants\PasswordPolicy;
use Illuminate\Support\Facades\DB;

class EloquentMemberProvisionalRegistrationRepository implements MemberProvisionalRegistrationRepositoryInterface
{
    public function upsert(string $email, string $token): void
    {
        DB::connection('user')->table('usr_provisional_registrations')->updateOrInsert(
            ['email' => $email],
            [
                'token'      => hash(PasswordPolicy::HASH_ALGORITHM, $token),
                'created_at' => now(),
            ]
        );
    }

    public function findByEmail(string $email): ?object
    {
        return DB::connection('user')
            ->table('usr_provisional_registrations')
            ->where('email', $email)
            ->first();
    }

    public function deleteByEmail(string $email): void
    {
        DB::connection('user')
            ->table('usr_provisional_registrations')
            ->where('email', $email)
            ->delete();
    }
}