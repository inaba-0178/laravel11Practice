<?php

declare(strict_types=1);

namespace App\Application\UseCases\EmailChange;

use App\Domain\EmailChange\Repositories\EmailChangeRepositoryInterface;
use App\Domain\EmailChange\ValueObjects\ChangeEmailRequest;
use App\Domain\EmailChange\ValueObjects\CurrentEmailRequest;
use InvalidArgumentException;
use RuntimeException;

final class UpdateEmailUseCase
{
    public function __construct(
        private readonly EmailChangeRepositoryInterface $emailChangeRepository,
    ) {}

    public function execute(ChangeEmailRequest $request, CurrentEmailRequest $currentEmailRequest): void
    {
        // 現在のメールアドレスの照合
        if ($currentEmailRequest->getCurrentEmail() !== $request->getCurrentEmail()) {
            throw new InvalidArgumentException('現在のメールアドレスが正しくありません。');
        }

        // 新メールアドレスの重複チェック
        if ($this->emailChangeRepository->isEmailTaken($request->getNewEmail(), $request->getUsrUserId())) {
            throw new RuntimeException('そのメールアドレスはすでに使用されています。');
        }

        $this->emailChangeRepository->updateEmail(
            usrUserId: $request->getUsrUserId(),
            newEmail:  $request->getNewEmail(),
        );
    }
}