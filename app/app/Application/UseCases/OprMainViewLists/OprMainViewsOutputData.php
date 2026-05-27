<?php

namespace App\Application\UseCases\OprMainViewLists;

use App\Domain\OprMainViewLists\Entities\OprMainView;

class OprMainViewsOutputData
{
    /**
     * @param OprMainView[] $views
     */
    public function __construct(
        private readonly array $views
    ) {}

    public function toArray(): array
    {
        return [
            'success' => true,
            'data'    => [
                'slides' => array_map(fn($view) => $view->toArray(), $this->views),
            ],
        ];
    }
}