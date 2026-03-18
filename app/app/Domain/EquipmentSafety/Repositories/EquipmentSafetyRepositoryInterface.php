<?php
 
namespace App\Domain\EquipmentSafety\Repositories;
 
use Illuminate\Support\Collection;
 
interface EquipmentSafetyRepositoryInterface
{
    public function getAll(): Collection;
}
 