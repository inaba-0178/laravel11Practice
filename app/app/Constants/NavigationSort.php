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

    // ディーラー管理系
    case DEALER_RESERVATION_LIST    = 1;
    case DEALER_RESERVATION_TODAY   = 2;
    case CAR_REGISTRATION           = 3;
    case DEALER_FEE                 = 4;
    case DEALER_SHOP                = 5;

    // 管理者機能
    case CAR_APPROVAL               = 1;
}