<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Mail\Repositories\MailTemplateRepositoryInterface;
use App\Infrastructure\Notifications\Mail\CommonMail;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

final class MailService
{
    public function __construct(
        private readonly MailTemplateRepositoryInterface $mailTemplateRepository,
    ) {}

    public function send(
        string $templateKey,
        string $toEmail,
        array  $placeholders = [],
        array  $attachments = [],
    ): void {
        $template = $this->mailTemplateRepository->findByKey($templateKey);

        if (!$template) {
            throw new RuntimeException('メールテンプレートが見つかりません。');
        }

        $body = $template->body;
        foreach ($placeholders as $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }

        Mail::to($toEmail)->send(new CommonMail(
            mailSubject:     $template->subject,
            body:            $body,
            mailAttachments: $attachments,  // ← 名前変更
        ));
    }
}