<?php

namespace App\Application\UseCases\ChangePassword;

use App\Domain\ChangePassword\Repositories\ChangePasswordRepositoryInterface;
use App\Domain\ChangePassword\ValueObjects\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordUseCase
{
    public function __construct(
        private readonly ChangePasswordRepositoryInterface $changePasswordRepository,
    ) {}

    public function execute(string $memberId, ChangePasswordRequest $request): void
    {
        $member = $this->changePasswordRepository->findById($memberId);

        if (!$member) {
            throw new \RuntimeException('会員情報が見つかりません');
        }

        if (!Hash::check($request->currentPassword, $member->password)) {
            throw new \InvalidArgumentException('現在のパスワードが正しくありません');
        }

        $this->changePasswordRepository->changePassword(
            $memberId,
            Hash::make($request->newPassword)
        );
    }
}