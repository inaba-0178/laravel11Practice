<?php

namespace App\Application\UseCases\FeaturedBodyTypeList;

use App\Domain\FeaturedBodyTypeList\Entities\BodyTypeInfo;

class FeaturedBodyTypeListOutputData
{
    private ?array $bodyTypeInfo;
    private int $count;

    /**
     * @param BodyTypeInfo[] $bodyTypeInfo
     */
    public function __construct(
        array $bodyTypeInfo,
    )
    {
        $this->bodyTypeInfo = $bodyTypeInfo;
        $this->count = count($bodyTypeInfo);
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'BodyTypeInfo' => array_map( fn($item) => $item instanceof BodyTypeInfo ? $item->toArray() : $item, $this->bodyTypeInfo),
                'count' => $this->count,
            ],
        ];
    }

    public function getBodyTypeInfo(): array
    {
        return $this->bodyTypeInfo;
    }

    public function getCount(): int
    {
        return $this->count;
    }

}