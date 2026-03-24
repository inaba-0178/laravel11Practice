<?php
namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OprMailTemplateSeeder extends Seeder
{
    public function run(): void
    {

        // 予約関連テンプレート追加
        DB::connection('mst')->table('opr_mail_templates')->insert([
            [
                'template_key'  => 'reservation_confirmed',
                'template_name' => '予約承認メール',
                'subject'       => 'ご予約確定のご連絡',
                'body'          => implode("\n", [
                    '<p>{{guest_name}}様</p>',
                    '<p>いつもお世話になっております。</p>',
                    '<p>{{dealer_name}}の{{staff_name}}でございます。</p>',
                    '<p>下記内容にてご予約を承りました。</p>',
                    '<p>日時：{{reservation_date}}</p>',
                    '<p>内容：{{reservation_type}}</p>',
                    '<p>ご希望車種：{{car_name}}</p>',
                    '<p>ご都合が悪くなられた場合は、お電話にてご連絡ください。</p>',
                    '<p>当日お会いできますことを心より楽しみにしております。</p>',
                    '<hr>',
                    '<p>{{dealer_name}}<br>担当：{{staff_name}}<br>住所：{{dealer_address}}<br>TEL：{{dealer_phone}}<br>MAIL：{{dealer_email}}<br>営業時間：{{dealer_hours}}</p>',
                ]),
                'placeholders'  => json_encode(['guest_name', 'dealer_name', 'staff_name', 'reservation_date', 'reservation_type', 'car_name', 'dealer_address', 'dealer_phone', 'dealer_email', 'dealer_hours']),
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'template_key'  => 'reservation_denial',
                'template_name' => '予約否認メール',
                'subject'       => 'ご予約希望日時についてのご相談',
                'body'          => implode("\n", [
                    '<p>{{guest_name}}様</p>',
                    '<p>いつもお世話になっております。</p>',
                    '<p>{{dealer_name}}の{{staff_name}}でございます。</p>',
                    '<p>大変恐れ入りますが、{{reservation_date}}の枠はすでに他のお客様のご予約で埋まっております。</p>',
                    '<p>誠に申し訳ございませんが、ご希望に添えず申し訳ございません。</p>',
                    '<p>ご都合のよろしい日時の候補をいくつかお知らせいただけますと幸いです。</p>',
                    '<hr>',
                    '<p>{{dealer_name}}<br>担当：{{staff_name}}<br>TEL：{{dealer_phone}}<br>MAIL：{{dealer_email}}</p>',
                ]),
                'placeholders'  => json_encode(['guest_name', 'dealer_name', 'staff_name', 'reservation_date', 'dealer_phone', 'dealer_email']),
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'template_key'  => 'reservation_cancelled_dealer',
                'template_name' => '店側都合キャンセルメール',
                'subject'       => 'ご予約内容に関する大切なお知らせ（お詫び）',
                'body'          => implode("\n", [
                    '<p>{{guest_name}}様</p>',
                    '<p>いつもお世話になっております。</p>',
                    '<p>{{dealer_name}}の{{staff_name}}でございます。</p>',
                    '<p>誠に心苦しいお知らせではございますが、{{guest_name}}様にご希望いただいておりました「{{car_name}}」が、他のお客様へのご成約により販売済みとなってしまいました。</p>',
                    '<p>ご期待を裏切る形となりましたこと、心よりお詫び申し上げます。</p>',
                    '<p>今後のご希望をご返信にてお聞かせいただけますと幸いです。</p>',
                    '<hr>',
                    '<p>{{dealer_name}}<br>担当：{{staff_name}}<br>TEL：{{dealer_phone}}<br>MAIL：{{dealer_email}}</p>',
                ]),
                'placeholders'  => json_encode(['guest_name', 'dealer_name', 'staff_name', 'car_name', 'dealer_phone', 'dealer_email']),
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'template_key'  => 'reservation_cancelled_customer',
                'template_name' => 'お客様都合キャンセルメール',
                'subject'       => 'ご予約キャンセルのご連絡ありがとうございます',
                'body'          => implode("\n", [
                    '<p>{{guest_name}}様</p>',
                    '<p>いつもお世話になっております。</p>',
                    '<p>{{dealer_name}}の{{staff_name}}でございます。</p>',
                    '<p>キャンセルの件、確かに承りました。</p>',
                    '<p>またご都合のよろしいタイミングで、改めてご相談いただけますと幸いです。</p>',
                    '<hr>',
                    '<p>{{dealer_name}}<br>担当：{{staff_name}}<br>TEL：{{dealer_phone}}<br>MAIL：{{dealer_email}}</p>',
                ]),
                'placeholders'  => json_encode(['guest_name', 'dealer_name', 'staff_name', 'dealer_phone', 'dealer_email']),
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'template_key'  => 'reservation_cancelled_dealer_trouble',
                'template_name' => '店側都合キャンセルメール（店舗トラブル）',
                'subject'       => 'ご予約内容に関する大切なご連絡',
                'body'          => implode("\n", [
                    '<p>{{guest_name}}様</p>',
                    '<p>いつもお世話になっております。</p>',
                    '<p>{{dealer_name}}の{{staff_name}}でございます。</p>',
                    '<p>この度は、{{reservation_date}}にご予約いただいております{{reservation_type}}につきまして、大切なご連絡がございます。</p>',
                    '<p>誠に申し訳ございませんが、当日の店舗体制の都合により、当初予定しておりました内容どおりのご案内が難しい状況となってしまいました。</p>',
                    '<p>つきましては、以下いずれかの形でご対応させていただければと存じます。</p>',
                    '<p>1. ご来店（またはオンライン商談／試乗）自体は予定どおりとし、可能な範囲で代替の担当者・内容にてご案内する。</p>',
                    '<p>2. 今回のご予約を一旦キャンセルとし、改めて別日程にてご予約を調整させていただく。</p>',
                    '<p>{{guest_name}}様のご都合・ご希望を最優先に考えたいと存じますので、上記のいずれをご希望か、本メールへのご返信にてお知らせいただけますでしょうか。</p>',
                    '<p>この度は、弊社の事情によりご迷惑とお手数をおかけいたしますこと、重ねて深くお詫び申し上げます。</p>',
                    '<hr>',
                    '<p>{{dealer_name}}<br>担当：{{staff_name}}<br>住所：{{dealer_address}}<br>TEL：{{dealer_phone}}<br>MAIL：{{dealer_email}}<br>営業時間：{{dealer_hours}}</p>',
                ]),
                'placeholders'  => json_encode(['guest_name', 'dealer_name', 'staff_name', 'reservation_date', 'reservation_type', 'dealer_address', 'dealer_phone', 'dealer_email', 'dealer_hours']),
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'template_key'  => 'reservation_auto_canceled_duplicate',
                'template_name' => '重複予約自動キャンセルメール',
                'subject'       => 'ご予約自動キャンセルのご連絡（重複予約）',
                'body'          => implode("\n", [
                    '<p>{{guest_name}}様</p>',
                    '<p>いつもお世話になっております。</p>',
                    '<p>{{dealer_name}}の{{staff_name}}でございます。</p>',
                    '<p>この度はご予約をいただき誠にありがとうございます。</p>',
                    '<p>同一のお時間帯に複数のご予約を確認いたしましたため、</p>',
                    '<p>誠に勝手ながら、下記のご予約につきましては重複分として自動キャンセルとさせていただきました。</p>',
                    '<p>日時：{{reservation_date}}</p>',
                    '<p>内容：{{reservation_type}}</p>',
                    '<p>ご希望車種：{{car_name}}</p>',
                    '<p>なお、同一日時で有効なご予約は、別途お送りしております「ご予約確定のご連絡」の内容どおりでございます。</p>',
                    '<p>内容のご確認やご不明な点がございましたら、お手数ですがお電話またはメールにてお問い合わせください。</p>',
                    '<p>今後とも何卒よろしくお願い申し上げます。</p>',
                    '<hr>',
                    '<p>{{dealer_name}}<br>担当：{{staff_name}}<br>住所：{{dealer_address}}<br>TEL：{{dealer_phone}}<br>MAIL：{{dealer_email}}<br>営業時間：{{dealer_hours}}</p>',
                ]),
                'placeholders'  => json_encode([
                    'guest_name',
                    'dealer_name',
                    'staff_name',
                    'reservation_date',
                    'reservation_type',
                    'car_name',
                    'dealer_address',
                    'dealer_phone',
                    'dealer_email',
                    'dealer_hours',
                ]),
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}