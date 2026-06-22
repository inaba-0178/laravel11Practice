<?php

namespace App\Domain\Common\Constants;

class CacheConstants
{
    /** mst系・中間テーブル系キャッシュ有効期限（1時間） */
    public const TTL_MST = 3600;

    /** 中古車情報取得系キャッシュ有効期限（10分） */
    public const TTL_CAR = 600;

    // mst系キャッシュキー
    public const KEY_BASIC_OPTIONS          = 'mst_basic_options';
    public const KEY_BODY_TYPES             = 'mst_body_types';
    public const KEY_COUNTRIES              = 'mst_countries';
    public const KEY_MILEAGE_LIST           = 'mst_mileage_list';
    public const KEY_PRICE_LIST             = 'mst_price_list';
    public const KEY_RIDING_CAPACITY_LIST   = 'mst_riding_capacity_list';
    public const KEY_REGIONS                = 'mst_regions';
    public const KEY_AREAS                  = 'mst_areas';
    public const KEY_MANUFACTURERS          = 'mst_manufacturers';
    public const KEY_MANUFACTURERS_ACTIVE   = 'mst_manufacturers_active';
    public const KEY_CAR_SERIES             = 'mst_car_series';
    public const KEY_DISPLACEMENT_LIST      = 'mst_displacement_list';

    // 中間テーブル系キャッシュキー
    public const KEY_CAR_TYPE_OPTIONS   = 'car_type_options';
    public const KEY_COLOR_OPTIONS      = 'color_options';
    public const KEY_DETAIL_OPTIONS     = 'detail_options';
    public const KEY_EQUIPMENT_BASIC    = 'equipment_basic';
    public const KEY_EQUIPMENT_DRESSUP  = 'equipment_dressup';
    public const KEY_EQUIPMENT_ENV      = 'equipment_env';
    public const KEY_EQUIPMENT_SAFETY   = 'equipment_safety';
    public const KEY_SEAT_OPTIONS       = 'seat_options';
    public const KEY_LOAN_DOWN_OPTIONS  = 'loan_down_options';
    public const KEY_LOAN_MONTHLY_OPTIONS = 'loan_monthly_options';

    // 中古車取得系キャッシュキー
    public const KEY_CAR_LIST           = 'car_list';
    public const KEY_CAR_DETAIL         = 'car_detail';
    public const KEY_CAR_COUNT          = 'car_count';
    public const KEY_SERIES_STK_COUNT   = 'series_stk_count';
    public const KEY_CAR_CONDITION_LIST = 'car_condition_list';
}
