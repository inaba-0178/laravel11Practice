<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatSettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mst')->table('opr_settings')->insertOrIgnore([
            [
                'key'         => 'chat_room_limit_free',
                'value'       => '3',
                'label'       => '無料ユーザーチャットルーム上限数',
                'description' => 'ユーザー同士のチャットルーム作成上限（無料プラン）',
            ],
        ]);
    }
}