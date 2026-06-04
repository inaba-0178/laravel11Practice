<?php

declare(strict_types=1);

namespace App\Application\UseCases\Analytics;

use App\Infrastructure\Repositories\Analytics\EloquentAnalyticsRepository;

/**
 * 閲覧数記録ユースケース
 *
 * 重複チェックを行い閲覧数をDBに記録する。
 */
class RecordViewCountUseCase
{
    public function __construct(
        private readonly EloquentAnalyticsRepository $repository,
    ) {}

    /**
     * 閲覧数を記録する
     *
     * @param RecordViewCountInputData $data
     * @return bool 記録した場合はtrue・重複の場合はfalse
     */
    public function execute(RecordViewCountInputData $data): bool
    {
        return $this->repository->recordIfNotDuplicate($data);
    }
}