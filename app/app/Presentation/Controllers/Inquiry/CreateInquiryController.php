<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Inquiry;

use App\Application\UseCases\Inquiry\CreateInquiryUseCase;
use App\Application\UseCases\Inquiry\CreateInquiryInputData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domain\Inquiry\ValueObjects\DealerId;
use App\Domain\Inquiry\ValueObjects\CarId;
use App\Domain\Inquiry\ValueObjects\InquiryType;
use App\Domain\Inquiry\ValueObjects\InquiryName;
use App\Domain\Inquiry\ValueObjects\InquiryEmail;
use App\Domain\Inquiry\ValueObjects\InquiryPhone;
use App\Domain\Inquiry\ValueObjects\InquiryPostalCode;
use App\Domain\Inquiry\ValueObjects\InquiryMessage;

class CreateInquiryController extends Controller
{
    public function __construct(
        private readonly CreateInquiryUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dealer_id'    => 'required|integer',
            'car_id'       => 'required|integer',
            'inquiry_type' => 'required|in:stock_check,estimate,condition_check,other',
            'name'         => 'nullable|string|max:100',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:20',
            'postal_code'  => 'nullable|string|max:8',
            'address'      => 'nullable|string|max:255',
            'message'      => 'nullable|string',
        ]);

        try {
            $data = new CreateInquiryInputData(
                dealerId:    new DealerId($validated['dealer_id']),
                carId:       new CarId($validated['car_id']),
                inquiryType: new InquiryType($validated['inquiry_type']),
                memberId:    null,
                name:        isset($validated['name'])        ? new InquiryName($validated['name'])               : null,
                email:       isset($validated['email'])       ? new InquiryEmail($validated['email'])             : null,
                phone:       isset($validated['phone'])       ? new InquiryPhone($validated['phone'])             : null,
                postalCode:  isset($validated['postal_code']) ? new InquiryPostalCode($validated['postal_code'])  : null,
                address:     $validated['address'] ?? null,
                message:     isset($validated['message'])     ? new InquiryMessage($validated['message'])         : null,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $this->useCase->execute($data);

        return response()->json(['success' => true]);
    }
}