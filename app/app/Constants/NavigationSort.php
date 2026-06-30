<?php
declare(strict_types=1);
namespace App\Constants;

enum NavigationSort :int
{
    case MST_AREA                   = 101;
    case MST_REGION                 = 102;
    case MST_PRICE_LIST             = 103;
    case MST_RIDING_CAPACITY_LIST   = 104;
    case MST_MILEAGE_LIST           = 105;
    case MST_MANUFACTURER           = 106;
    case MST_BODY_TYPE              = 107;
    case MST_CAR_SERIES             = 108;
    case MST_VEHICLE_WEIGHT_TAX     = 109;
    case MST_LIABILITY_INSURANCE    = 110;
    case MST_BASIC_OPTION           = 111;
    case MST_COUNTRY                = 112;
    case MST_VEHICLE                = 113;
    case MST_VEHICLE_TAX            = 114;
    case MST_SEAT_OPTION            = 115;
    case MST_LOAN_PLAN              = 116;
    case MST_DISPLACEMENT_LIST      = 117;
    case MST_FEATURED_BRAND         = 118;

    // opr系
    case OPR_MAIL_TEMPLATE          = 201;
    case OPR_MAIN_VIEW              = 202;
    case OPR_SETTINGS               = 203;

    // ディーラー管理系
    case DEALER_RESERVATION_LIST    = 301;
    case DEALER_RESERVATION_TODAY   = 302;
    case CAR_REGISTRATION           = 303;
    case DEALER_FEE                 = 304;
    case DEALER_SHOP                = 305;
    case DEALER_STAFF               = 306;
    case DEALER_CONTENT             = 307;
    case DEALER_REVIEW              = 308;
    case AFFILIATED_STORE           = 309;
    case BULK_CAR_UPLOAD            = 310;
    case DEALER_SCHEDULE            = 311;
    case INQUIRY_LIST               = 312;
    case CAR_STOCK                  = 313;
    case CHAT                       = 314;

    // 管理者機能
    case CAR_APPROVAL               = 401;
    case MST_VERSION                = 402;
    case MST_UPLOAD                 = 403;
    case MAINTENANCE                = 404;
    case BULK_CAR_APPROVAL          = 405;

    // システム
    case USER_MANAGEMENT            = 501;

    // ユーザー機能
    case USER_PASSWORD              = 601;
}