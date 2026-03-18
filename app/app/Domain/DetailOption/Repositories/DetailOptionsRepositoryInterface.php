<?php
 
namespace App\Domain\DetailOption\Repositories;
 
use Illuminate\Support\Collection;
 
interface DetailOptionsRepositoryInterface
{
    public function getAll(): Collection;
}
 