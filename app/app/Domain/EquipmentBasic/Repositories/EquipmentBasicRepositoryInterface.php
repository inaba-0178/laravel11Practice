<?php
 
namespace App\Domain\EquipmentBasic\Repositories;
 
use Illuminate\Support\Collection;
 
interface EquipmentBasicRepositoryInterface
{
    public function getAll(): Collection;
}
 