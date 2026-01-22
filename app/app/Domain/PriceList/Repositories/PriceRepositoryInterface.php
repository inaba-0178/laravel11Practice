<?php
namespace App\Domain\PriceList\Repositories;

interface PriceRepositoryInterface
{
    /**
     * すべてのPriceを取得
     * 
     * @return Price[]
     */
    public function findAll(): array;
}