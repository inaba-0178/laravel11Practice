<?php
 
namespace App\Providers;
 
use App\Domain\EquipmentSafety\Repositories\EquipmentSafetyRepositoryInterface;
use App\Infrastructure\Repositories\EquipmentSafety\EloquentEquipmentSafetyRepository;
use Illuminate\Support\ServiceProvider;
 
class EquipmentSafetyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            EquipmentSafetyRepositoryInterface::class,
            EloquentEquipmentSafetyRepository::class,
        );
    }
}
 