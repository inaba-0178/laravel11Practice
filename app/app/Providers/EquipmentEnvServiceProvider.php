<?php
 
namespace App\Providers;
 
use App\Domain\EquipmentEnv\Repositories\EquipmentEnvRepositoryInterface;
use App\Infrastructure\Repositories\EquipmentEnv\EloquentEquipmentEnvRepository;
use Illuminate\Support\ServiceProvider;
 
class EquipmentEnvServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            EquipmentEnvRepositoryInterface::class,
            EloquentEquipmentEnvRepository::class,
        );
    }
}
 