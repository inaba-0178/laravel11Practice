<?php

declare(strict_types=1);

namespace App\Application\UseCases\Inquiry;

use App\Application\UseCases\Inquiry\CreateInquiryInputData;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use App\Infrastructure\Repositories\Inquiry\EloquentInquiryRepository;
use App\Notifications\InquiryNotification;

class CreateInquiryUseCase
{
    public function __construct(
        private readonly EloquentInquiryRepository $repository,
    ) {}

    public function execute(CreateInquiryInputData $data): void
    {
        $inquiry = $this->repository->create($data);

        // ディーラーにメール通知
        $dealer = StkCarDealer::find($data->dealerId->getValue());
        if ($dealer?->email) {
            $dealer->notify(new InquiryNotification($inquiry));
        }
    }
}