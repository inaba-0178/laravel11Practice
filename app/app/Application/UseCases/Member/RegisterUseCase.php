<?php

namespace App\Application\UseCases\Member;

use App\Domain\Member\Repositories\MemberProvisionalRegistrationRepositoryInterface;
use App\Domain\Member\Repositories\MemberRepositoryInterface;
use App\Domain\Member\ValueObjects\RegisterRequest;
use App\Domain\Shared\Constants\PasswordPolicy;
use Illuminate\Support\Facades\Hash;

class RegisterUseCase
{
    public function __construct(
        private readonly MemberRepositoryInterface                        $memberRepository,
        private readonly MemberProvisionalRegistrationRepositoryInterface $provisionalRegistrationRepository,
    ) {}

    public function execute(RegisterRequest $request): void
    {
        $record = $this->provisionalRegistrationRepository->findByEmail($request->email);

        if (!$record) {
            throw new \RuntimeException('無効なトークンです');
        }

        if (!hash_equals($record->token, hash(PasswordPolicy::HASH_ALGORITHM, $request->token))) {
            throw new \RuntimeException('無効なトークンです');
        }

        if (now()->gt(\Carbon\Carbon::parse($record->created_at)->addMinutes(PasswordPolicy::RESET_TOKEN_EXPIRE_MINUTES))) {
            throw new \RuntimeException('トークンの有効期限が切れています');
        }

        $this->memberRepository->create([
            'sei'          => $request->sei,
            'mei'          => $request->mei,
            'sei_kana'     => $request->sei_kana,
            'mei_kana'     => $request->mei_kana,
            'birth_date'   => $request->birth_date,
            'post_code'    => $request->post_code,
            'prefecture'   => $request->prefecture,
            'city'         => $request->city,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'phone_number' => $request->phone_number,
            'gender'       => $request->gender,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        $this->provisionalRegistrationRepository->deleteByEmail($request->email);
    }
}