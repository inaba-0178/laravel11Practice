<?php
declare(strict_types=1);
namespace App\Constants;

enum NavigationGroup :string
{

    case MST_GROUP          = "マスタ参照";
    case OPR_GROUP          = "設定";
    case DEALER_GROUP       = "ディーラー管理";
    case ADMIN_GROUP        = "管理者機能";
    case SYSTEM_GROUP       = "システム管理";
    case USER_GROUP         = "ユーザー機能";
    case MST_UPDATE_GROUP   = "マスタ更新";
}