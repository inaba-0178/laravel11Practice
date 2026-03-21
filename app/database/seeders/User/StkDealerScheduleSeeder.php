<?php

namespace Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StkDealerScheduleSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('user')->statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::connection('user')->table('stk_dealer_schedules')->truncate();
        DB::connection('user')->statement('SET FOREIGN_KEY_CHECKS=1;');

        $schedules = [];
        $now       = Carbon::now();
        $dealerId  = 1;

        // 予約種別ID
        // 1: 来店予約 2: 試乗予約（不可） 3: オンライン商談
        $reservationTypeIds = [1, 3];

        // 今月・来月分のスケジュールを作成
        for ($monthOffset = 0; $monthOffset <= 1; $monthOffset++) {
            $month     = $now->copy()->addMonths($monthOffset);
            $daysInMonth = $month->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $month->copy()->setDay($day);

                // 過去日はスキップ
                if ($date->lt($now->copy()->startOfDay())) {
                    continue;
                }

                // 水曜日は定休日としてスキップ
                if ($date->dayOfWeek === Carbon::WEDNESDAY) {
                    continue;
                }

                foreach ($reservationTypeIds as $typeId) {
                    // 午前の枠
                    $schedules[] = [
                        'dealer_id'           => $dealerId,
                        'reservation_type_id' => $typeId,
                        'date'                => $date->toDateString(),
                        'time_from'           => '10:00:00',
                        'time_to'             => '12:00:00',
                        'max_reservations'    => 2,
                        'is_available'        => 1,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ];

                    // 午後の枠
                    $schedules[] = [
                        'dealer_id'           => $dealerId,
                        'reservation_type_id' => $typeId,
                        'date'                => $date->toDateString(),
                        'time_from'           => '14:00:00',
                        'time_to'             => '17:00:00',
                        'max_reservations'    => 2,
                        'is_available'        => 1,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ];
                }
            }
        }

        // 500件ずつ分割してinsert
        foreach (array_chunk($schedules, 500) as $chunk) {
            DB::connection('user')->table('stk_dealer_schedules')->insert($chunk);
        }
    }
}