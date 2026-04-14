<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\User\StkDealerFeeSeeder;
use Database\Seeders\Mst\CarRegistrationMailTemplateSeeder;
use Database\Seeders\Mst\MstEquipmentBasicSeeder;
use Database\Seeders\Mst\MstSeatOptionSeeder;
use Database\Seeders\Mst\MailTemplateSeeder;
use Database\Seeders\Mst\MstEquipmentDressupSeeder;
use Database\Seeders\Mst\MstVehicleWeightTaxSeeder;
use Database\Seeders\Mst\MstAreasSeeder;
use Database\Seeders\Mst\MstEquipmentEnvSeeder;
use Database\Seeders\Mst\MstVehicleYeaRversionsSeeder;
use Database\Seeders\Mst\MstBasicOptionSeeder;
use Database\Seeders\Mst\MstEquipmentSafetySeeder;
use Database\Seeders\Mst\MstVehiclesSeeder;
use Database\Seeders\Mst\MstBodyTypeImagesSeeder;
use Database\Seeders\Mst\MstFeaturedBodyTypesSeeder;
use Database\Seeders\Mst\OprCardEalersSeeder;
use Database\Seeders\Mst\MstBodyTypesSeeder;
use Database\Seeders\Mst\MstFeaturedBrandsSeeder;
use Database\Seeders\Mst\OprCarsSeeder;
use Database\Seeders\Mst\MstCarSeriesBodyTypesSeeder;
use Database\Seeders\Mst\MstLiabilityInsuranceSeeder;
use Database\Seeders\Mst\OprLoanDownOptionSeeder;
use Database\Seeders\Mst\MstCarSeriesSeeder;
use Database\Seeders\Mst\MstManufacturerImagesSeeder;
use Database\Seeders\Mst\OprLoanMonthlyOptionSeeder;
use Database\Seeders\Mst\MstCarTypeOptionSeeder;
use Database\Seeders\Mst\MstManufacturersSeeder;
use Database\Seeders\Mst\OprMailTemplateSeeder;
use Database\Seeders\Mst\MstColorOptionSeeder;
use Database\Seeders\Mst\MstMileageListsSeeder;
use Database\Seeders\Mst\OprReservationTypeSeeder;
use Database\Seeders\Mst\MstDetailOptionSeeder;
use Database\Seeders\Mst\MstPriceListsSeeder;
use Database\Seeders\Mst\MstDisplacementListsSeeder;
use Database\Seeders\Mst\MstRidingCapacityListsSeeder;


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
            //CarRegistrationMailTemplateSeeder::class,
            //MstEquipmentBasicSeeder::class,
            //MstSeatOptionSeeder::class,
            // MstEquipmentDressupSeeder::class,
            // MstVehicleWeightTaxSeeder::class,
            // MstAreasSeeder::class,
            // MstEquipmentEnvSeeder::class,
            // MstBasicOptionSeeder::class,
            // MstEquipmentSafetySeeder::class,
            // MstLiabilityInsuranceSeeder::class,
            // OprLoanDownOptionSeeder::class,
            // MstCarSeriesSeeder::class,
            // OprLoanMonthlyOptionSeeder::class,
            // MstCarTypeOptionSeeder::class,
            // MstManufacturersSeeder::class,
            // OprMailTemplateSeeder::class,
            // MstColorOptionSeeder::class,
            // MstMileageListsSeeder::class,
            // OprReservationTypeSeeder::class,
            // MstDetailOptionSeeder::class,
            // MstPriceListsSeeder::class,
            // MstDisplacementListsSeeder::class,
            // MstRidingCapacityListsSeeder::class,
            
            //MstVehiclesSeeder::class,
            //MstVehicleYeaRversionsSeeder::class,
            //MailTemplateSeeder::class,
            //OprCardEalersSeeder::class,
            //OprCarsSeeder::class,
            // MstBodyTypesSeeder::class,
            // MstCarSeriesBodyTypesSeeder::class,
            // MstManufacturerImagesSeeder::class,
            // MstBodyTypeImagesSeeder::class,
            //StkDealerFeeSeeder::class,

            MstFeaturedBrandsSeeder::class,
            //MstFeaturedBodyTypesSeeder::class,
        ]);
    }
}
