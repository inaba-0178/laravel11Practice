<?php
 
namespace App\Providers;
 
use App\Domain\EquipmentDressup\Repositories\EquipmentDressupRepositoryInterface;
use App\Infrastructure\Repositories\EquipmentDressup\EloquentEquipmentDressupRepository;
use Illuminate\Support\ServiceProvider;
 
class EquipmentDressupServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            EquipmentDressupRepositoryInterface::class,
            EloquentEquipmentDressupRepository::class,
        );
    }
}
 