<?php

namespace App\Infrastructure\Repositories\ChangePassword;

use App\Domain\ChangePassword\Repositories\ChangePasswordRepositoryInterface;
use App\Infrastructure\Eloquent\User\Member;

class EloquentChangePasswordRepository implements ChangePasswordRepositoryInterface
{
    public function findById(string $id): ?object
    {
        return Member::find($id);
    }

    public function changePassword(string $id, string $hashedPassword): void
    {
        Member::where('id', $id)->update([
            'password' => $hashedPassword,
        ]);
    }
}