<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use  Database\Seeders\Mst\MailTemplateSeeder;
use Database\Seeders\Mst\MstColorOptionSeeder;
use Database\Seeders\Mst\MstBasicOptionSeeder;
use Database\Seeders\Mst\MstDetailOptionSeeder;
use Database\Seeders\Mst\MstEquipmentBasicSeeder;
use Database\Seeders\Mst\MstEquipmentSafetySeeder;
use Database\Seeders\Mst\MstEquipmentEnvSeeder;
use Database\Seeders\Mst\MstEquipmentDressupSeeder;
use Database\Seeders\Mst\MstSeatOptionSeeder;
use Database\Seeders\Mst\MstVehicleWeightTaxSeeder;
use Database\Seeders\Mst\MstLiabilityInsuranceSeeder;
use Database\Seeders\User\StkDealerFeeSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            //MailTemplateSeeder::class,
            // MstColorOptionSeeder::class,
            // MstBasicOptionSeeder::class,
            // MstDetailOptionSeeder::class,
            // MstEquipmentBasicSeeder::class,
            // MstEquipmentSafetySeeder::class,
            // MstEquipmentEnvSeeder::class,
            // MstEquipmentDressupSeeder::class,
            // MstSeatOptionSeeder::class,
            MstVehicleWeightTaxSeeder::class,
            MstLiabilityInsuranceSeeder::class,
            StkDealerFeeSeeder::class,
        ]);
    }
}
