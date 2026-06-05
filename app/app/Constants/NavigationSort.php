<?php
declare(strict_types=1);
namespace App\Constants;

enum NavigationSort :int
{
    case MST_AREA                   = 1;
    case MST_REGION                 = 2;
    case MST_PRICE_LIST             = 3;
    case MST_RIDING_CAPACITY_LIST   = 4;
    case MST_MILEAGE_LIST           = 5;
    case MST_MANUFACTURER           = 6;
    case MST_BODY_TYPE              = 7;
    case MST_CAR_SERIES             = 8;
    case MST_VEHICLE_WEIGHT_TAX     = 9;
    case MST_LIABILITY_INSURANCE    = 10;

    //opr系
    case OPR_MAIL_TEMPLATE          = 1;
    case OPR_MAIN_VIEW              = 2;
    case OPR_SETTINGS               = 3;

    // ディーラー管理系
    case DEALER_RESERVATION_LIST    = 1;
    case DEALER_RESERVATION_TODAY   = 2;
    case CAR_REGISTRATION           = 3;
    case DEALER_FEE                 = 4;
    case DEALER_SHOP                = 5;
    case DEALER_STAFF               = 6;
    case DEALER_CONTENT             = 7;
    case DEALER_REVIEW              = 8;
    case AFFILIATED_STORE           = 9;
    case BULK_CAR_UPLOAD            = 10;
    case DEALER_SCHEDULE            = 11;

    // 管理者機能
    case CAR_APPROVAL               = 1;
    case MST_VERSION                = 2;
    case MST_UPLOAD                 = 3;
    case MAINTENANCE                = 4;
    case BULK_CAR_APPROVAL          = 5;

    //　システム
    case USER_MANAGEMENT            = 1;

    //　ユーザー機能
    case USER_PASSWORD              = 1;
}