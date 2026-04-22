<?php

namespace App\Infrastructure\Repositories\ChangePassword;

use App\Domain\ChangePassword\Repositories\ChangePasswordRepositoryInterface;
use App\Infrastructure\Eloquent\User\UsrUser;

class EloquentChangePasswordRepository implements ChangePasswordRepositoryInterface
{
    public function findById(string $id): ?object
    {
        return UsrUser::find($id);
    }

    public function changePassword(string $id, string $hashedPassword): void
    {
        UsrUser::where('id', $id)->update([
            'password' => $hashedPassword,
        ]);
    }
}