<?php
 
namespace App\Domain\BasicOption\Repositories;
 
use Illuminate\Support\Collection;
 
interface BasicOptionsRepositoryInterface
{
    public function getAll(): Collection;
}
 