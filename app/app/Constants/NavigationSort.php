<?php
declare(strict_types=1);
namespace App\Constants;

enum NavigationSort :int
{
    case MST_AREA                   = 1;
    case MST_REGION                 = 2;
    case MST_PRICE_LIST             = 3;
    case MST_RIDING_CAPACITY_LIST   = 4;
}