<?php

declare(strict_types=1);

namespace App\Domain\CarUpload\DataTransformers;

use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\Mst\MstVehicles;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use App\Infrastructure\Eloquent\Mst\MstEquipmentSafety;
use App\Infrastructure\Eloquent\Mst\MstEquipmentBasic;
use App\Infrastructure\Eloquent\Mst\MstEquipmentDressup;
use App\Infrastructure\Eloquent\Mst\MstEquipmentEnv;
use App\Infrastructure\Eloquent\Mst\MstSeatOption;
use App\Infrastructure\Eloquent\User\StkDealerFee;
use App\Constants\AudioOption;
use App\Constants\NaviOption;

class BulkCarRowTransformer
{
    // スプレッドシートのENUM表記 → DB値マッピング
    private const PRICE_DISPLAY_TYPE_MAP = [
        'actual（実際の価格）' => 'actual',
        'negotiable（要相談）' => 'negotiable',
        'ask（応相談）'        => 'ask',
    ];

    private const COLOR_GROUP_MAP = [
        'white（白系）'  => 'white',
        'black（黒系）'  => 'black',
        'silver（銀系）' => 'silver',
        'red（赤系）'    => 'red',
        'blue（青系）'   => 'blue',
        'green（緑系）'  => 'green',
        'brown（茶系）'  => 'brown',
        'yellow（黄系）' => 'yellow',
        'pink（ピンク系）' => 'pink',
        'other（その他）' => 'other',
    ];

    private const REPAIR_HISTORY_MAP = [
        'none（なし）'       => 'none',
        'minor（軽微）'      => 'minor',
        'major（修復歴あり）' => 'major',
        'unknown（不明）'    => 'unknown',
    ];

    private const FUEL_TYPE_MAP = [
        'gasoline（ガソリン）'   => 'gasoline',
        'diesel（ディーゼル）'   => 'diesel',
        'hybrid（ハイブリッド）' => 'hybrid',
        'electric（電気）'       => 'electric',
        'phev（PHEV）'           => 'phev',
        'other（その他）'        => 'other',
    ];

    private const INSPECTION_STATUS_MAP = [
        'available（車検あり）' => 'available',
        'none（車検なし）'      => 'none',
        'new_car（新車）'       => 'new_car',
    ];

    private const STEERING_WHEEL_MAP = [
        'right（右ハンドル）' => 'right',
        'left（左ハンドル）'  => 'left',
    ];

    private const SLIDE_DOOR_MAP = [
        'none（なし）'          => 'none',
        'right_only（右のみ）'  => 'right_only',
        'both_manual（両側手動）' => 'both_manual',
        'both_power（両側電動）'  => 'both_power',
        'right_power（右電動）'   => 'right_power',
        'left_power（左電動）'    => 'left_power',
    ];

    private const DRIVE_SYSTEM_MAP = [
        '2WD' => '2WD',
        '4WD' => '4WD',
        'AWD' => 'AWD',
        'FR'  => 'FR',
        'FF'  => 'FF',
        'MR'  => 'MR',
        'RR'  => 'RR',
    ];

    private const TRANSMISSION_MAP = [
        'AT'           => 'AT',
        'MT'           => 'MT',
        'CVT'          => 'CVT',
        'DCT'          => 'DCT',
        'other（その他）' => 'other',
    ];

    private const OPTION_CATEGORY_MAP = [
        'basic（快適装備）'       => 'basic',
        'safety（安全装備）'      => 'safety',
        'environmental（環境装備）' => 'environmental',
        'audio（オーディオ）'     => 'audio',
        'navigation（ナビ）'      => 'navigation',
        'dress_up（エクステリア）' => 'dress_up',
        'seat（インテリア）'      => 'seat',
        'other（その他）'         => 'other',
    ];

    /**
     * 1行のスプレッドシートデータをstk_cars登録用データに変換
     */
    public function transformCarData(array $row, int $dealerId): array
    {
        $manufacturer = MstManufacturers::where('display_name', trim($row['メーカー']))->first();
        $series       = MstCarSeries::where('series_name', trim($row['車体名']))->first();
        $vehicle      = MstVehicles::where('name', trim($row['年式・グレード']))->first();
        $region       = MstRegions::where('name', trim($row['地域（都道府県）']))->first();
        $bodyType     = !empty($row['ボディタイプ'])
            ? MstBodyTypes::where('name', trim($row['ボディタイプ']))->first()
            : null;
        $dealerFee    = !empty($row['諸費用プラン'])
            ? StkDealerFee::where('dealer_id', $dealerId)->where('name', trim($row['諸費用プラン']))->first()
            : null;

        return [
            'dealer_id'          => $dealerId,
            'manufacturer_id'    => $manufacturer?->id,
            'series_id'          => $series?->series_id,
            'vehicle_id'         => $vehicle?->id,
            'status'             => 'draft',
            'price'              => (int)$row['支払価格（円）'],
            'price_display_type' => self::PRICE_DISPLAY_TYPE_MAP[trim($row['価格表示方法'])] ?? 'actual',
            'mileage'            => (int)$row['走行距離（km）'],
            'color'              => trim($row['ボディカラー']),
            'color_group'        => self::COLOR_GROUP_MAP[trim($row['色系統'] ?? '')] ?? null,
            'repair_history'     => self::REPAIR_HISTORY_MAP[trim($row['修復歴'] ?? '')] ?? 'unknown',
            'fuel_type'          => self::FUEL_TYPE_MAP[trim($row['燃料タイプ'] ?? '')] ?? null,
            'body_type_id'       => $bodyType?->id,
            'region_id'          => $region?->id,
            'model_year'         => !empty($row['年式']) ? (int)$row['年式'] : null,
            'recycle_fee'        => !empty($row['リサイクル預託金（円）']) ? (int)$row['リサイクル預託金（円）'] : null,
            'dealer_fee_id'      => $dealerFee?->id,
            'transmission'       => self::TRANSMISSION_MAP[trim($row['ミッション'] ?? '')] ?? null,
            'bulk_upload_key'    => trim($row['ユニークID']),
        ];
    }

    /**
     * 1行のスプレッドシートデータをstk_car_details登録用データに変換
     */
    public function transformDetailData(array $row): array
    {
        return [
            'first_registration_date' => !empty($row['初回登録日']) ? trim($row['初回登録日']) : null,
            'inspection_expire_date'  => !empty($row['車検満了日']) ? trim($row['車検満了日']) : null,
            'inspection_status'       => self::INSPECTION_STATUS_MAP[trim($row['車検状態'] ?? '')] ?? 'available',
            'drive_system'            => self::DRIVE_SYSTEM_MAP[trim($row['駆動方式'] ?? '')] ?? null,
            'displacement'            => !empty($row['排気量（cc）']) ? (int)$row['排気量（cc）'] : 0,
            'steering_wheel'          => self::STEERING_WHEEL_MAP[trim($row['ハンドル'] ?? '')] ?? 'right',
            'number_of_doors'         => !empty($row['ドア数']) ? (int)$row['ドア数'] : 0,
            'slide_door'              => self::SLIDE_DOOR_MAP[trim($row['スライドドア'] ?? '')] ?? 'none',
            'riding_capacity'         => !empty($row['乗車定員']) ? (int)$row['乗車定員'] : null,
            'loan_available'          => trim($row['ローン可'] ?? '') === '○' ? 1 : null,
            'description'             => !empty($row['車両説明文']) ? trim($row['車両説明文']) : null,
        ];
    }

    /**
     * 1行のスプレッドシートデータからstk_car_options登録用データ配列を生成
     */
    public function transformOptionsData(array $row): array
    {
        $options = [];
        $order   = 1;

        // 安全装備
        $safetyMap = [
            'ABS'                    => 'abs',
            '運転席エアバッグ'        => 'driver_airbag',
            'サポカー'               => 'support_car',
            '助手席エアバッグ'        => 'pass_airbag',
            '衝突被害軽減ブレーキ'    => 'collision_brake',
            'サイドエアバッグ'        => 'side_airbag',
            'クルーズコントロール'     => 'cruise',
            'カーテンエアバッグ'      => 'curtain_airbag',
            'アダプティブクルーズコントロール' => 'adaptive_cruise',
            '頭部衝撃緩和ヘッドレスト' => 'knee_airbag',
            'レーンキープアシスト'     => 'lane_keep',
            'フロントカメラ'          => 'front_camera',
            'パーキングアシスト'       => 'parking_assist',
            'サイドカメラ'            => 'side_camera',
            'アクセル踏み間違い防止装置' => 'no_accel_error',
            'バックカメラ'            => 'back_camera',
            '横滑り防止装置'          => 'no_slip',
            '全周囲カメラ'            => 'around_camera',
            '障害物センサー'          => 'obstacle',
            'ブラインドスポットモニター' => 'blind_spot',
            'リアトラフィックモニタ'   => 'rear_traffic',
            'ヒルディセントコントロール' => 'hill_descent',
        ];
        foreach ($safetyMap as $col => $value) {
            if (trim($row[$col] ?? '') === '○') {
                $options[] = ['option_category' => 'safety', 'option_name' => $value, 'display_order' => $order++];
            }
        }

        // 快適装備
        $basicMap = [
            'キーレスエントリー'     => 'keyless',
            'ETC'                   => 'etc',
            'スマートキー'           => 'smart_key',
            '盗難防止装置'           => 'anti_theft',
            'パワーウィンドウ'        => 'power_window',
            'サンルーフ・ガラスルーフ' => 'sunroof',
            'パワステ'              => 'power_seat',
            'ルーフレール'           => 'roof_rail',
            'エアコン・クーラー'      => 'ac',
            '後席モニター'           => 'rear_monitor',
            'Wエアコン'             => 'dual_ac',
            'エアサスペンション'      => 'air_sus',
            'ディスチャージヘッドランプ' => 'discharge',
            '1500W給電'             => 'power_1500',
            'フロントフォグランプ'    => 'front_fog',
            'ドライブレコーダー'      => 'drive_rec',
            'オートマチックハイビーム' => 'auto_beam',
            '電動開閉バックドア'      => 'power_back',
            'LEDヘッドライト'        => 'led',
            'ディスプレイオーディオ'  => 'display_audio',
            'アダプティブヘッドライト' => 'adaptive_head',
        ];
        foreach ($basicMap as $col => $value) {
            if (trim($row[$col] ?? '') === '○') {
                $options[] = ['option_category' => 'basic', 'option_name' => $value, 'display_order' => $order++];
            }
        }

        // インテリア
        $seatMap = [
            'フルフラットシート' => 'flat_seat',
            '本革シート'        => 'leather_seat',
            '3列シート'         => '3row_seat',
            'ベンチシート'       => 'bench_seat',
            'ウォークスルー'     => 'walkthrough',
            '電動シート'         => 'electric_seat',
            'シートヒーター'     => 'seat_heater',
            'オットマン'         => 'ottoman',
            'シートエアコン'     => 'seat_ac',
        ];
        foreach ($seatMap as $col => $value) {
            if (trim($row[$col] ?? '') === '○') {
                $options[] = ['option_category' => 'seat', 'option_name' => $value, 'display_order' => $order++];
            }
        }

        // エクステリア
        $dressupMap = [
            'フルエアロ'   => 'full_aero',
            'ローダウン'   => 'lowdown',
            'アルミホイール' => 'alloy_wheel',
            '全塗装済'     => 'full_paint',
            'リフトアップ' => 'lift_up',
        ];
        foreach ($dressupMap as $col => $value) {
            if (trim($row[$col] ?? '') === '○') {
                $options[] = ['option_category' => 'dress_up', 'option_name' => $value, 'display_order' => $order++];
            }
        }

        // 環境装備
        $envMap = [
            'アイドリングストップ' => 'idling_stop',
            'エコカー減税対象車'   => 'eco_car',
        ];
        foreach ($envMap as $col => $value) {
            if (trim($row[$col] ?? '') === '○') {
                $options[] = ['option_category' => 'environmental', 'option_name' => $value, 'display_order' => $order++];
            }
        }

        // オーディオ
        $audioMap = [
            'CD再生'    => AudioOption::CD,
            'DVD再生'   => AudioOption::DVD,
            'Bluetooth' => AudioOption::BLUETOOTH,
            'USB'       => AudioOption::USB,
        ];
        foreach ($audioMap as $col => $value) {
            if (trim($row[$col] ?? '') === '○') {
                $options[] = ['option_category' => 'audio', 'option_name' => $value, 'display_order' => $order++];
            }
        }
        if (!empty($row['オーディオメーカー'])) {
            $options[] = ['option_category' => 'audio', 'option_name' => 'maker_' . trim($row['オーディオメーカー']), 'display_order' => $order++];
        }

        // ナビ
        $naviMap = [
            'カーナビあり' => NaviOption::NAVI,
            'TVあり'      => NaviOption::TV,
            'DVDナビあり' => NaviOption::DVD_NAVI,
        ];
        foreach ($naviMap as $col => $value) {
            if (trim($row[$col] ?? '') === '○') {
                $options[] = ['option_category' => 'navigation', 'option_name' => $value, 'display_order' => $order++];
            }
        }

        // 特徴（special_type）
        $specialMap = [
            'ワンオーナー'     => 'one_owner',
            'キャンピングカー' => 'camping_car',
            '福祉車両'        => 'welfare_car',
            '登録済未使用車'   => 'unused',
            'エコカー減税対象' => 'eco_car',
            '未登録車'        => 'unregistered',
        ];
        foreach ($specialMap as $col => $value) {
            if (trim($row[$col] ?? '') === '○') {
                $options[] = ['option_category' => 'special_type', 'option_name' => $value, 'display_order' => $order++];
            }
        }

        // 販売・サービス
        $salesMap = [
            '車両品質評価書付き' => 'quality_cert',
            '購入プラン付き'     => 'purchase_plan',
            'アフター保証対象車' => 'sensor_after',
            'オンライン相談可'   => 'online_consult',
        ];
        foreach ($salesMap as $col => $value) {
            if (trim($row[$col] ?? '') === '○') {
                $options[] = ['option_category' => 'other', 'option_name' => $value, 'display_order' => $order++];
            }
        }

        // その他オプション（最大5件）
        for ($i = 1; $i <= 5; $i++) {
            $catKey  = "その他オプション{$i}カテゴリ";
            $nameKey = "その他オプション{$i}名称";
            $cat     = trim($row[$catKey] ?? '');
            $name    = trim($row[$nameKey] ?? '');
            if (!empty($cat) && !empty($name)) {
                $catValue = self::OPTION_CATEGORY_MAP[$cat] ?? explode('（', $cat)[0];
                $options[] = ['option_category' => $catValue, 'option_name' => $name, 'display_order' => $order++];
            }
        }

        return $options;
    }
}