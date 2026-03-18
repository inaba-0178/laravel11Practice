<?php
 
namespace App\Domain\CarTypeOption\Repositories;
 
use Illuminate\Support\Collection;
 
interface CarTypeOptionRepositoryInterface
{
    public function getAll(): Collection;
}
 