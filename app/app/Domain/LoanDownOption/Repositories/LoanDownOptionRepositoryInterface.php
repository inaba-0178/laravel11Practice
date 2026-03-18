<?php
 
namespace App\Domain\LoanDownOption\Repositories;
 
use Illuminate\Support\Collection;
 
interface LoanDownOptionRepositoryInterface
{
    public function getAll(): Collection;
}
 