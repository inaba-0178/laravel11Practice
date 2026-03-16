<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'id'            => 1,
                'template_name' => '会員仮登録',
                'subject'       => '会員登録のご案内',
                'body'          => implode('', [
                    '<h2>会員登録のご案内</h2>',
                    '<p>以下のボタンから会員登録を完了してください。</p>',
                    '<a href="{{url}}" class="button">会員登録を完了する</a>',
                    '<p class="expire">このリンクは30分間有効です。</p>',
                    '<p class="expire">心当たりがない場合は無視してください。</p>',
                ]),
                'placeholders'  => json_encode(['token', 'email', 'url']),
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'id'            => 2,
                'template_name' => 'パスワードリセット',
                'subject'       => 'パスワードリセットのご案内',
                'body'          => implode('', [
                    '<h2>パスワードリセットのご案内</h2>',
                    '<p>以下のボタンからパスワードの再設定を行ってください。</p>',
                    '<a href="{{url}}" class="button">パスワードを再設定する</a>',
                    '<p class="expire">このリンクは30分間有効です。</p>',
                    '<p class="expire">心当たりがない場合は無視してください。</p>',
                ]),
                'placeholders'  => json_encode(['token', 'email', 'url']),
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        DB::connection('mst')->table('opr_mail_templates')->insert($templates);
    }
}