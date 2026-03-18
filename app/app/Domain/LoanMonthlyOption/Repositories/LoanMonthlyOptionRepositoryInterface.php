<?php
 
namespace App\Domain\LoanMonthlyOption\Repositories;
 
use Illuminate\Support\Collection;
 
interface LoanMonthlyOptionRepositoryInterface
{
    public function getAll(): Collection;
}
 