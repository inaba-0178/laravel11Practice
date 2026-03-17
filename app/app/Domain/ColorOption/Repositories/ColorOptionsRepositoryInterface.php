<?php
 
namespace App\Domain\ColorOption\Repositories;
 
use Illuminate\Support\Collection;
 
interface ColorOptionsRepositoryInterface
{
    public function getAll(): Collection;
}
 