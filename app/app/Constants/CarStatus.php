<?php

declare(strict_types=1);

namespace App\Constants;

class CarStatus
{
    const DRAFT     = 'draft';
    const PENDING   = 'pending';
    const AVAILABLE = 'available';
    const RESERVED  = 'reserved';
    const SOLD      = 'sold';
    const REJECTED  = 'rejected';
    const DELETED   = 'deleted';

    const LABELS = [
        self::DRAFT     => '下書き',
        self::PENDING   => '承認待ち',
        self::AVAILABLE => '公開中',
        self::RESERVED  => '予約中',
        self::SOLD      => '売却済み',
        self::REJECTED  => '差し戻し',
        self::DELETED   => '削除済み',
    ];

    const COLORS = [
        self::DRAFT     => 'gray',
        self::PENDING   => 'warning',
        self::AVAILABLE => 'success',
        self::RESERVED  => 'info',
        self::SOLD      => 'gray',
        self::REJECTED  => 'danger',
        self::DELETED   => 'gray',
    ];

    // 承認依頼ボタンを表示するステータス
    const CAN_REQUEST_APPROVAL = [
        self::DRAFT,
        self::PENDING,
        self::REJECTED,
        self::AVAILABLE,
    ];

    // 削除ボタンを表示するステータス
    const CAN_DELETE = [
        self::DRAFT,
        self::PENDING,
        self::REJECTED,
    ];
}