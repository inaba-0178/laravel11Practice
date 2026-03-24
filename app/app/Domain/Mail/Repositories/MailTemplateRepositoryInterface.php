<?php

declare(strict_types=1);

namespace App\Domain\Mail\Repositories;

use App\Infrastructure\Eloquent\Opr\OprMailTemplate;

interface MailTemplateRepositoryInterface
{
    public function findByKey(string $key): ?OprMailTemplate;
}