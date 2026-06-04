<?php

declare(strict_types=1);

namespace App\Application\UseCases\Inquiry;

use App\Domain\Inquiry\ValueObjects\DealerId;
use App\Domain\Inquiry\ValueObjects\CarId;
use App\Domain\Inquiry\ValueObjects\InquiryType;
use App\Domain\Inquiry\ValueObjects\InquiryName;
use App\Domain\Inquiry\ValueObjects\InquiryEmail;
use App\Domain\Inquiry\ValueObjects\InquiryPhone;
use App\Domain\Inquiry\ValueObjects\InquiryPostalCode;
use App\Domain\Inquiry\ValueObjects\InquiryMessage;

final class CreateInquiryInputData
{
    public function __construct(
        public readonly DealerId           $dealerId,
        public readonly CarId              $carId,
        public readonly InquiryType        $inquiryType,
        public readonly ?string            $memberId,
        public readonly ?InquiryName       $name,
        public readonly ?InquiryEmail      $email,
        public readonly ?InquiryPhone      $phone,
        public readonly ?InquiryPostalCode $postalCode,
        public readonly ?string            $address,
        public readonly ?InquiryMessage    $message,
    ) {}
}