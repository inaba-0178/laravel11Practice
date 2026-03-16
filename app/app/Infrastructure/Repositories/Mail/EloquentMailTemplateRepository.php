<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Mail;

use App\Domain\Mail\Repositories\MailTemplateRepositoryInterface;
use App\Infrastructure\Eloquent\Opr\OprMailTemplate;

final class EloquentMailTemplateRepository implements MailTemplateRepositoryInterface
{
    public function findById(int $id): ?OprMailTemplate
    {
        return OprMailTemplate::find($id);
    }
}