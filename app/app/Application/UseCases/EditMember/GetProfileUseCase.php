<?php

namespace App\Application\UseCases\EditMember;

use App\Domain\EditMember\Repositories\EditMemberRepositoryInterface;

class GetProfileUseCase
{
    public function __construct(
        private readonly EditMemberRepositoryInterface $editMemberRepository,
    ) {}

    public function execute(string $id): GetProfileOutputData
    {
        $member = $this->editMemberRepository->findById($id);

        if (!$member) {
            throw new \RuntimeException('ユーザーが見つかりません');
        }

        return new GetProfileOutputData($member);
    }
}