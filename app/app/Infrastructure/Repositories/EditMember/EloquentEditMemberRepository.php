<?php

namespace App\Infrastructure\Repositories\EditMember;

use App\Domain\EditMember\Repositories\EditMemberRepositoryInterface;
use App\Domain\Member\Entities\Member;
use App\Infrastructure\Eloquent\User\Member as EloquentMember;

class EloquentEditMemberRepository implements EditMemberRepositoryInterface
{
    public function findById(string $id): ?Member
    {
        $eloquentMember = EloquentMember::find($id);

        if (!$eloquentMember) {
            return null;
        }

        return $this->toEntity($eloquentMember);
    }

    public function update(string $id, array $data): Member
    {
        $eloquentMember = EloquentMember::find($id);
        if (!$eloquentMember) {
            throw new \RuntimeException('ユーザーが見つかりません');
        }
        $eloquentMember->update($data);
        return $this->toEntity($eloquentMember->fresh());
    }

    private function toEntity(EloquentMember $eloquentMember): Member
    {
        return new Member(
            id:                $eloquentMember->id,
            sei:               $eloquentMember->sei,
            mei:               $eloquentMember->mei,
            sei_kana:          $eloquentMember->sei_kana,
            mei_kana:          $eloquentMember->mei_kana,
            birth_date:        $eloquentMember->birth_date,
            post_code:         $eloquentMember->post_code,
            prefecture:        $eloquentMember->prefecture,
            city:              $eloquentMember->city,
            address_line1:     $eloquentMember->address_line1,
            address_line2:     $eloquentMember->address_line2,
            phone_number:      $eloquentMember->phone_number,
            gender:            $eloquentMember->gender,
            email:             $eloquentMember->email,
            email_verified_at: $eloquentMember->email_verified_at?->toDateTimeString(),
            email_changed_at:  $eloquentMember->email_changed_at?->toDateTimeString(),
        );
    }
}