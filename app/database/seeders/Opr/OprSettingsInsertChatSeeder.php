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

        DB::connection('mst')->table('opr_settings')->insert([
            [
                'key'         => 'chat_invite_expire_hours',
                'value'       => '72',
                'label'       => 'チャット招待有効時間',
                'description' => 'チャット招待の有効期限（時間）',
            ],
        ]);
    }
}