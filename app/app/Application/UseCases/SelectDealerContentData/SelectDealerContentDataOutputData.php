<?php

declare(strict_types=1);

namespace App\Application\UseCases\SelectDealerContentData;

final class SelectDealerContentDataOutputData
{
    public function __construct(
        private readonly array $contents,
    ) {}

    public function toArray(): array
    {
        return [
            'success'  => true,
            'contents' => array_map(fn ($content) => $content->toArray(), $this->contents),
        ];
    }
}