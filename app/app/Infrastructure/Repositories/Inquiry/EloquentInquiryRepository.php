<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Inquiry;

use App\Application\UseCases\Inquiry\CreateInquiryInputData;
use App\Infrastructure\Eloquent\User\StkInquiry;

class EloquentInquiryRepository
{
    public function create(CreateInquiryInputData $data): StkInquiry
    {
        return StkInquiry::create([
            'dealer_id'    => $data->dealerId->getValue(),
            'car_id'       => $data->carId->getValue(),
            'member_id'    => $data->memberId,
            'status'       => 'new',
            'inquiry_type' => $data->inquiryType->getValue(),
            'name'         => $data->name?->getValue(),
            'email'        => $data->email?->getValue(),
            'phone'        => $data->phone?->getValue(),
            'postal_code'  => $data->postalCode?->getValue(),
            'address'      => $data->address,
            'message'      => $data->message?->getValue(),
        ]);
    }
}