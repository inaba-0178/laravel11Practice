<?php

declare(strict_types=1);

namespace App\Constants;

class CarStatus
{
    const DRAFT             = 'draft';
    const PENDING           = 'pending';
    const APPROVED_PENDING  = 'approved_pending';
    const AVAILABLE         = 'available';
    const RESERVED          = 'reserved';
    const SOLD              = 'sold';
    const REJECTED          = 'rejected';
    const DELETED           = 'deleted';
    const SCHEDULED         = 'scheduled';
    const PUBLISH_ENDED     = 'publish_ended';

    const LABELS = [
        self::DRAFT             => '下書き',
        self::PENDING           => '承認待ち',
        self::APPROVED_PENDING  => '承認済み公開前',
        self::AVAILABLE         => '公開中',
        self::RESERVED          => '予約中',
        self::SOLD              => '売却済み',
        self::REJECTED          => '差し戻し',
        self::DELETED           => '削除済み',
        self::SCHEDULED         => '公開日時指定済み',
        self::PUBLISH_ENDED     => '公開終了',
    ];

    const COLORS = [
        self::DRAFT             => 'gray',
        self::PENDING           => 'warning',
        self::APPROVED_PENDING  => 'info',
        self::AVAILABLE         => 'success',
        self::RESERVED          => 'info',
        self::SOLD              => 'gray',
        self::REJECTED          => 'danger',
        self::DELETED           => 'gray',
        self::SCHEDULED         => 'info',
        self::PUBLISH_ENDED     => 'gray',
    ];

    // 承認依頼ボタンを表示するステータス
    const CAN_REQUEST_APPROVAL = [
        self::DRAFT,
        self::PENDING,
        self::AVAILABLE,
    ];

    // 削除ボタンを表示するステータス
    const CAN_DELETE = [
        self::DRAFT,
        self::PENDING,
        self::REJECTED,
    ];

    // 車両登録ページ用
    const STATUS_CONFIG = [
        self::DRAFT => [
            'label'  => '下書き',
            'bg'     => '#f3f4f6',
            'color'  => '#374151',
            'border' => '#d1d5db',
            'icon'   => '📝',
            'description' => '保存のみされている状態です。承認依頼を送ると管理者が確認します。',
        ],
        self::PENDING => [
            'label'  => '承認待ち',
            'bg'     => '#fef3c7',
            'color'  => '#92400e',
            'border' => '#fcd34d',
            'icon'   => '⏳',
            'description' => '管理者が確認中です。承認されるまでお待ちください。',
        ],
        self::REJECTED => [
            'label'  => '差し戻し',
            'bg'     => '#fef2f2',
            'color'  => '#991b1b',
            'border' => '#fca5a5',
            'icon'   => '⚠️',
            'description' => '管理者から差し戻しがあります。指摘内容を確認して再申請してください。',
        ],
        self::AVAILABLE => [
            'label'  => '承認済み',
            'bg'     => '#f0fdf4',
            'color'  => '#15803d',
            'border' => '#86efac',
            'icon'   => '✅',
            'description' => '承認済みで公開中です。編集すると再度承認が必要になります。',
        ],
        self::APPROVED_PENDING => [
            'label'       => '承認済み公開前',
            'bg'          => '#eff6ff',
            'color'       => '#1d4ed8',
            'border'      => '#bfdbfe',
            'icon'        => '🔒',
            'description' => '承認済みです。管理者が公開処理を行うまでお待ちください。',
        ],
        self::SCHEDULED => [
            'label'       => '公開日時指定済み',
            'bg'          => '#f0f9ff',
            'color'       => '#0369a1',
            'border'      => '#bae6fd',
            'icon'        => '🕐',
            'description' => '公開日時が設定されています。指定日時になると自動で公開されます。',
        ],
        self::PUBLISH_ENDED => [
            'label'       => '公開終了',
            'bg'          => '#f3f4f6',
            'color'       => '#374151',
            'border'      => '#d1d5db',
            'icon'        => '🔚',
            'description' => '公開期間が終了しました。再公開する場合は期間設定または公開ボタンを押してください。',
        ],
    ];
}