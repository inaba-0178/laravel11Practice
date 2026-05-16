<?php

declare(strict_types=1);

namespace App\Constants\Role;

use App\Constants\RoleConstants;

final class RoleManagement
{
    // ===== 運営側 =====

    // 管理ツールにアクセスできる
    public const ADMIN_ROLES = [
        RoleConstants::SUPER,
        RoleConstants::ADMIN,
        RoleConstants::STAFF,
        RoleConstants::TESTER,
    ];

    // ===== マスタ更新 =====

    // アップロード・承認申請可能
    public const MST_OPERATOR_ROLES = [
        RoleConstants::SUPER,
        RoleConstants::ADMIN,
        RoleConstants::STAFF,
    ];

    // 承認・却下・有効化可能
    public const MST_APPROVER_ROLES = [
        RoleConstants::SUPER,
        RoleConstants::ADMIN,
    ];

    // ===== 車両管理 =====

    // 車両承認可能（CarApprovalResource）
    public const CAR_APPROVER_ROLES = [
        RoleConstants::SUPER,
        RoleConstants::ADMIN,
        RoleConstants::STAFF,
    ];

    // ===== メンテナンス =====

    // メンテナンスモード操作可能
    public const MAINTENANCE_ROLES = [
        RoleConstants::SUPER,
        RoleConstants::ADMIN,
    ];

    // ===== ディーラー =====

    // ディーラー側ロール
    public const DEALER_ROLES = [
        RoleConstants::DEALER,
        RoleConstants::DEALER_STAFF,
    ];

    // dealer_idが必要なロール
    public const DEALER_ID_REQUIRED_ROLES = [
        RoleConstants::DEALER,
        RoleConstants::DEALER_STAFF,
    ];

    // ===== 口コミ管理（DealerReviewResource） =====

    // 口コミ削除可能
    public const REVIEW_ADMIN_ROLES = [
        RoleConstants::SUPER,
        RoleConstants::ADMIN,
        RoleConstants::STAFF,
    ];

    // 口コミ閲覧可能
    public const REVIEW_ACCESS_ROLES = [
        RoleConstants::SUPER,
        RoleConstants::ADMIN,
        RoleConstants::STAFF,
        RoleConstants::DEALER,
        RoleConstants::DEALER_STAFF,
    ];

    // ===== 系列店・提携店（AffiliatedStoreResource） =====

    // アクセス可能
    public const AFFILIATED_STORE_ACCESS_ROLES = [
        RoleConstants::SUPER,
        RoleConstants::ADMIN,
        RoleConstants::DEALER,
        RoleConstants::DEALER_STAFF,
    ];

    // 強制解除可能
    public const AFFILIATED_STORE_FORCE_DISSOLVE_ROLES = [
        RoleConstants::SUPER,
        RoleConstants::ADMIN,
    ];

    // ===== ユーザー管理（UserResource） =====
    public const USER_MANAGEMENT_ACCESS_ROLES = [
        RoleConstants::SUPER,
        RoleConstants::ADMIN,
        RoleConstants::DEALER,
    ];
}