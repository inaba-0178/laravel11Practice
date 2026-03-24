<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Mail;

use App\Domain\Mail\Repositories\MailTemplateRepositoryInterface;
use App\Infrastructure\Eloquent\Opr\OprMailTemplate;

final class EloquentMailTemplateRepository implements MailTemplateRepositoryInterface
{
    public function findByKey(string $key): ?OprMailTemplate
    {
        return OprMailTemplate::where('template_key', $key)->first();
    }
}