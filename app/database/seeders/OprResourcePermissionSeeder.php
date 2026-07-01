<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Constants\RoleConstants;
use App\Infrastructure\Eloquent\Opr\OprResourcePermission;
use Illuminate\Database\Seeder;

class OprResourcePermissionSeeder extends Seeder
{
    private const SUPER_ADMIN        = [RoleConstants::SUPER, RoleConstants::ADMIN];
    private const SUPER_ADMIN_STAFF  = [RoleConstants::SUPER, RoleConstants::ADMIN, RoleConstants::STAFF];
    private const ALL_OPR            = [RoleConstants::SUPER, RoleConstants::ADMIN, RoleConstants::STAFF, RoleConstants::TESTER];
    private const DEALER_ALL         = [RoleConstants::SUPER, RoleConstants::ADMIN, RoleConstants::STAFF, RoleConstants::DEALER, RoleConstants::DEALER_STAFF];
    private const ALL_ROLES          = [RoleConstants::SUPER, RoleConstants::ADMIN, RoleConstants::STAFF, RoleConstants::TESTER, RoleConstants::DEALER, RoleConstants::DEALER_STAFF];

    public function run(): void
    {
        $permissions = [
            // ===== マスタ参照 =====
            ['resource_key' => 'MstAreasResource',               'resource_label' => 'エリア一覧',               'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstAreaDetail',                  'resource_label' => 'エリア詳細',               'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstBasicOptionsResource',        'resource_label' => '基本オプション一覧',       'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstBasicOptionDetail',           'resource_label' => '基本オプション詳細',       'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstBodyTypesResource',           'resource_label' => 'ボディタイプ一覧',         'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstBodyTypeDetail',              'resource_label' => 'ボディタイプ詳細',         'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstCarSeriesResource',           'resource_label' => '車両一覧',                 'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstCarSeriesDetail',             'resource_label' => '車両シリーズ詳細',         'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstCarTypeOptionResource',       'resource_label' => '車種タイプオプション一覧', 'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstCarTypeOptionDetail',         'resource_label' => '車種タイプオプション詳細', 'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstColorOptionsResource',        'resource_label' => 'カラーオプション一覧',     'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstColorOptionsDetail',          'resource_label' => 'カラーオプション詳細',     'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstCountriesResource',           'resource_label' => '国一覧',                   'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstCountryDetail',               'resource_label' => '国詳細',                   'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstDetailOptionsResource',       'resource_label' => '詳細オプション一覧',       'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstDetailOptionsDetail',         'resource_label' => '詳細オプション詳細',       'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstDisplacementListsResource',   'resource_label' => '排気量リスト一覧',         'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstDisplacementListDetail',      'resource_label' => '排気量リスト詳細',         'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstEquipmentBasicResource',      'resource_label' => '基本装備一覧',             'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstEquipmentBasicDetail',        'resource_label' => '基本装備詳細',             'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstEquipmentDressupResource',    'resource_label' => 'ドレスアップ装備一覧',     'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstEquipmentDressupDetail',      'resource_label' => 'ドレスアップ装備詳細',     'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstEquipmentEnvResource',        'resource_label' => '環境装備一覧',             'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstEquipmentEnvDetail',          'resource_label' => '環境装備詳細',             'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstEquipmentSafetyResource',     'resource_label' => '安全装備一覧',             'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstEquipmentSafetyDetail',       'resource_label' => '安全装備詳細',             'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstFeaturedBodyTypesResource',   'resource_label' => '注目ボディタイプ一覧',     'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstFeaturedBodyTypeDetail',      'resource_label' => '注目ボディタイプ詳細',     'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstFeaturedBrandsResource',      'resource_label' => '注目ブランド一覧',         'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstFeaturedBrandDetail',         'resource_label' => '注目ブランド詳細',         'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstLiabilityInsuranceResource',  'resource_label' => '自賠責保険一覧',           'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstLiabilityInsuranceDetail',    'resource_label' => '自賠責保険詳細',           'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstLoanPlanResource',            'resource_label' => 'ローンプラン一覧',         'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstLoanPlanDetail',              'resource_label' => 'ローンプラン詳細',         'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstManufacturersResource',       'resource_label' => 'メーカー一覧',             'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstManufacturerDetail',          'resource_label' => 'メーカー詳細',             'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstMileageListsResource',        'resource_label' => '走行距離リスト一覧',       'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstMileageListDetail',           'resource_label' => '走行距離リスト詳細',       'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstPriceListsResource',          'resource_label' => '価格リスト一覧',           'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstPriceListDetail',             'resource_label' => '価格リスト詳細',           'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstRegionsResource',             'resource_label' => '地域一覧',                 'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstRegionDetail',                'resource_label' => '地域詳細',                 'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstRidingCapacityListsResource', 'resource_label' => '乗車定員リスト一覧',       'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstRidingCapacityListDetail',    'resource_label' => '乗車定員リスト詳細',       'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstSeatOptionResource',          'resource_label' => 'シートオプション一覧',     'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstSeatOptionDetail',            'resource_label' => 'シートオプション詳細',     'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstVehicleTaxResource',          'resource_label' => '自動車税一覧',             'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstVehicleTaxDetail',            'resource_label' => '自動車税詳細',             'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstVehicleWeightTaxResource',    'resource_label' => '自動車重量税一覧',         'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstVehicleWeightTaxDetail',      'resource_label' => '自動車重量税詳細',         'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstVehiclesResource',            'resource_label' => '車両マスタ一覧',           'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstVehicleDetail',               'resource_label' => '車両バージョン選択',       'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstVehicleYearVersionDetail',    'resource_label' => '車両バージョン詳細',       'resource_group' => 'マスタ参照', 'allowed_roles' => self::ALL_OPR],
            ['resource_key' => 'MstVersionResource',             'resource_label' => 'バージョン管理',           'resource_group' => 'マスタ参照', 'allowed_roles' => self::SUPER_ADMIN_STAFF],
            ['resource_key' => 'MstUpload',                      'resource_label' => 'マスタアップロード',       'resource_group' => 'マスタ参照', 'allowed_roles' => self::SUPER_ADMIN_STAFF],

            // ===== 運営系 =====
            ['resource_key' => 'OprMainViewResource',    'resource_label' => 'メインビュー管理',       'resource_group' => '運営系', 'allowed_roles' => self::SUPER_ADMIN_STAFF],
            ['resource_key' => 'OprMainViewDetail',      'resource_label' => 'メインビュー詳細',       'resource_group' => '運営系', 'allowed_roles' => self::SUPER_ADMIN_STAFF],
            ['resource_key' => 'OprSettingResource',     'resource_label' => 'システム設定',           'resource_group' => '運営系', 'allowed_roles' => self::SUPER_ADMIN],
            ['resource_key' => 'MailTemplateResource',   'resource_label' => 'メールテンプレート',     'resource_group' => '運営系', 'allowed_roles' => self::SUPER_ADMIN_STAFF],
            ['resource_key' => 'MaintenancePage',        'resource_label' => 'メンテナンス設定',       'resource_group' => '運営系', 'allowed_roles' => self::SUPER_ADMIN],

            // ===== 車両管理 =====
            ['resource_key' => 'CarRegistrationResource',  'resource_label' => '車両登録',           'resource_group' => '車両管理', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'CarStockResource',         'resource_label' => '車両在庫',           'resource_group' => '車両管理', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'CarApprovalResource',      'resource_label' => '車両承認',           'resource_group' => '車両管理', 'allowed_roles' => self::SUPER_ADMIN_STAFF],
            ['resource_key' => 'BulkCarApprovalResource',  'resource_label' => '一括車両承認',       'resource_group' => '車両管理', 'allowed_roles' => self::SUPER_ADMIN_STAFF],
            ['resource_key' => 'BulkCarUploadPage',        'resource_label' => '一括車両アップロード', 'resource_group' => '車両管理', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'BulkCarBatchDetailPage',   'resource_label' => '一括アップロード詳細', 'resource_group' => '車両管理', 'allowed_roles' => self::DEALER_ALL],

            // ===== ディーラー機能 =====
            ['resource_key' => 'DealerShopResource',         'resource_label' => '店舗管理',         'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerShopDetail',           'resource_label' => '店舗詳細',         'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerStaffResource',        'resource_label' => 'スタッフ管理',     'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerStaffDetail',          'resource_label' => 'スタッフ詳細',     'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerContentResource',      'resource_label' => 'コンテンツ管理',   'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerContentDetail',        'resource_label' => 'コンテンツ詳細',   'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerFeeResource',          'resource_label' => '手数料管理',       'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerFeeDetail',            'resource_label' => '手数料詳細',       'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerReviewResource',       'resource_label' => '口コミ管理',       'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerReviewDetail',         'resource_label' => '口コミ詳細',       'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerReviewReplyDetail',    'resource_label' => '口コミ返信詳細',   'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerLoanResource',         'resource_label' => 'ローン管理',       'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'DealerSchedulePage',         'resource_label' => 'スケジュール',     'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'AffiliatedStoreResource',    'resource_label' => '系列店管理',       'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'AffiliatedStoreDetail',      'resource_label' => '系列店詳細',       'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'ReservationResource',        'resource_label' => '予約一覧',         'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'TodayReservationResource',   'resource_label' => '本日の予約',       'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'InquiryResource',            'resource_label' => 'お問い合わせ',     'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'ChatResource',               'resource_label' => 'チャット',         'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'LoanSettingResource',        'resource_label' => 'ローン設定',       'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],
            ['resource_key' => 'LoanSettingRequestPage',     'resource_label' => 'ローン設定申請',   'resource_group' => 'ディーラー機能', 'allowed_roles' => self::DEALER_ALL],

            // ===== ユーザー管理 =====
            ['resource_key' => 'UserResource',   'resource_label' => 'ユーザー管理', 'resource_group' => 'ユーザー管理', 'allowed_roles' => self::SUPER_ADMIN],
            ['resource_key' => 'UserDetail',     'resource_label' => 'ユーザー詳細', 'resource_group' => 'ユーザー管理', 'allowed_roles' => self::SUPER_ADMIN],

            // ===== 全員アクセス可能 =====
            ['resource_key' => 'Dashboard',        'resource_label' => 'ダッシュボード',   'resource_group' => '共通', 'allowed_roles' => self::ALL_ROLES],
            ['resource_key' => 'ChangePassword',   'resource_label' => 'パスワード変更',   'resource_group' => '共通', 'allowed_roles' => self::ALL_ROLES],
        ];

        foreach ($permissions as $permission) {
            OprResourcePermission::updateOrCreate(
                ['resource_key' => $permission['resource_key']],
                [
                    'resource_label' => $permission['resource_label'],
                    'resource_group' => $permission['resource_group'],
                    'allowed_roles'  => $permission['allowed_roles'],
                ]
            );
        }
    }
}
