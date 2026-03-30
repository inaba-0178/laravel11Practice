<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarRegistrationMailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mst')->table('opr_mail_templates')->insert([
            [
                'template_key'  => 'car_registration_approved_dealer',
                'template_name' => '車両登録承認通知（ディーラー宛）',
                'subject'       => '【車両登録承認のお知らせ】{{car_name}}',
                'body'          => <<<BODY
{{dealer_name}} 担当者様

お世話になっております。
ご登録いただいた車両が承認されました。

■ 車両情報
車両名：{{car_name}}
登録日時：{{registered_at}}
承認日時：{{approved_at}}

■ 管理者からのコメント
{{admin_comment}}

引き続きよろしくお願いいたします。
BODY,
                'placeholders'  => json_encode([
                    'dealer_name',
                    'car_name',
                    'registered_at',
                    'approved_at',
                    'admin_comment',
                ]),
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'template_key'  => 'car_registration_rejected_dealer',
                'template_name' => '車両登録差し戻し通知（ディーラー宛）',
                'subject'       => '【車両登録差し戻しのお知らせ】{{car_name}}',
                'body'          => <<<BODY
{{dealer_name}} 担当者様

お世話になっております。
ご登録いただいた車両について、内容を確認した結果、差し戻しとなりましたのでご連絡いたします。

■ 車両情報
車両名：{{car_name}}
登録日時：{{registered_at}}
差し戻し日時：{{rejected_at}}

■ 差し戻し理由
{{reject_reason}}

お手数ですが、内容をご確認のうえ再度ご登録をお願いいたします。
BODY,
                'placeholders'  => json_encode([
                    'dealer_name',
                    'car_name',
                    'registered_at',
                    'rejected_at',
                    'reject_reason',
                ]),
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}