<?php

namespace App\Application\UseCases\EditMember;

use App\Domain\EditMember\Repositories\EditMemberRepositoryInterface;
use App\Domain\Member\Entities\Member;

class GetProfileUseCase
{
    public function __construct(
        private readonly EditMemberRepositoryInterface $editMemberRepository,
    ) {}

    public function execute(string $id): Member
    {
        $member = $this->editMemberRepository->findById($id);

        if (!$member) {
            throw new \RuntimeException('ユーザーが見つかりません');
        }

        return $member;
    }
}