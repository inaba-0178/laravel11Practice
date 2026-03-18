<?php
 
namespace App\Domain\EquipmentDressup\Repositories;
 
use Illuminate\Support\Collection;
 
interface EquipmentDressupRepositoryInterface
{
    public function getAll(): Collection;
}
 