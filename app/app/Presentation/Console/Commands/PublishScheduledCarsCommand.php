<?php

declare(strict_types=1);

namespace App\Presentation\Console\Commands;

use App\Application\UseCases\CarStock\PublishScheduledCarsUseCase;
use Illuminate\Console\Command;

class PublishScheduledCarsCommand extends Command
{
    protected $signature   = 'cars:publish-scheduled';
    protected $description = '公開日時が来た車両を自動公開・公開終了する';

    public function handle(PublishScheduledCarsUseCase $useCase): void
    {
        $result = $useCase->execute();
        $this->info("公開: {$result['published']}台 / 公開終了: {$result['unpublished']}台");
    }
}