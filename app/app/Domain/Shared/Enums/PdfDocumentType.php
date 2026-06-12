<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

class PdfDocumentType
{
    const ESTIMATE = 'estimate';
    const CONTRACT = 'contract';

    const TITLES = [
        self::ESTIMATE => 'お 見 積 書',
        self::CONTRACT => '自動車売買注文書（兼契約書）',
    ];

    const NUMBER_LABELS = [
        self::ESTIMATE => '見積番号',
        self::CONTRACT => '注文番号',
    ];

    const SHOW_SIGNATURE_AREA = [
        self::ESTIMATE => false,
        self::CONTRACT => true,
    ];

    const SHOW_CONTRACT_NOTE = [
        self::ESTIMATE => false,
        self::CONTRACT => true,
    ];
}