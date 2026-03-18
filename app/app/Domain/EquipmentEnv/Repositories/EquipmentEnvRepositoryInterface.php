<?php
 
namespace App\Domain\EquipmentEnv\Repositories;
 
use Illuminate\Support\Collection;
 
interface EquipmentEnvRepositoryInterface
{
    public function getAll(): Collection;
}
 