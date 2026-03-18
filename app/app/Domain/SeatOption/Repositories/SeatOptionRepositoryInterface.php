<?php
 
namespace App\Domain\SeatOption\Repositories;
 
use Illuminate\Support\Collection;
 
interface SeatOptionRepositoryInterface
{
    public function getAll(): Collection;
}
 