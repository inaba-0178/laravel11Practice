<?php
 
namespace App\Providers;
 
use App\Domain\EquipmentBasic\Repositories\EquipmentBasicRepositoryInterface;
use App\Infrastructure\Repositories\EquipmentBasic\EloquentEquipmentBasicRepository;
use Illuminate\Support\ServiceProvider;
 
class EquipmentBasicServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            EquipmentBasicRepositoryInterface::class,
            EloquentEquipmentBasicRepository::class,
        );
    }
}
 