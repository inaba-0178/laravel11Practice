<?php

namespace App\Application\UseCases\EditMember;

use App\Domain\EditMember\Repositories\EditMemberRepositoryInterface;
use App\Domain\EditMember\ValueObjects\UpdateProfileRequest;
use App\Domain\Member\Entities\Member;

class UpdateProfileUseCase
{
    public function __construct(
        private readonly EditMemberRepositoryInterface $editMemberRepository,
    ) {}

    public function execute(string $id, UpdateProfileRequest $request): Member
    {
        $member = $this->editMemberRepository->findById($id);

        if (!$member) {
            throw new \RuntimeException('ユーザーが見つかりません');
        }

        return $this->editMemberRepository->update($id, [
            'sei'           => $request->sei,
            'mei'           => $request->mei,
            'sei_kana'      => $request->sei_kana,
            'mei_kana'      => $request->mei_kana,
            'birth_date'    => $request->birth_date,
            'post_code'     => $request->post_code,
            'prefecture'    => $request->prefecture,
            'city'          => $request->city,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'phone_number'  => $request->phone_number,
            'gender'        => $request->gender,
        ]);
    }
}