<?php

namespace App\Application\UseCases\FeaturedBrandList;

use App\Domain\FeaturedBrandList\Entities\FeaturedBrand;

class FeaturedBrandListOutputData
{
    private ?array $featuredBrands;
    private int $count;

    /**
     * @param FeaturedBrands[] $featuredBrands
     */
    public function __construct(array $featuredBrands)
    {
        $this->featuredBrands = $featuredBrands;
        $this->count = count($featuredBrands);
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'FeaturedBrandList' => array_map(fn(FeaturedBrand $featuredBrand) => $featuredBrand->toArray(), $this->featuredBrands),
                'count' => $this->count,
            ],
        ];
    }

    public function FeaturedBrands(): array
    {
        return $this->featuredBrands;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}