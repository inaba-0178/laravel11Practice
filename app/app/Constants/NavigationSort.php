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

    //opr系
    case OPR_MAIL_TEMPLATE          = 1;
}