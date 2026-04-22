<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\StoreDealerReview;

use App\Application\UseCases\StoreDealerReview\StoreDealerReviewUseCase;
use App\Application\UseCases\StoreDealerReview\StoreDealerReviewInputData;
use App\Domain\StoreDealerReview\ValueObjects\DealerId;
use App\Domain\StoreDealerReview\ValueObjects\Nickname;
use App\Domain\StoreDealerReview\ValueObjects\ReviewComment;
use App\Domain\StoreDealerReview\ValueObjects\PurchasedCar;
use App\Domain\StoreDealerReview\ValueObjects\PurchasedAt;
use App\Domain\StoreDealerReview\ValueObjects\GuestName;
use App\Domain\StoreDealerReview\ValueObjects\GuestPhone;
use App\Domain\StoreDealerReview\ValueObjects\GuestEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;
use App\Infrastructure\Eloquent\User\UsrUser;



class StoreDealerReviewController extends Controller
{
    public function __construct(
        private readonly StoreDealerReviewUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'dealer_id'         => 'required|integer|min:1',
                'nickname'          => 'required|string|max:20',
                'rating'            => 'required|integer|min:1|max:5',
                'rating_service'    => 'nullable|integer|min:1|max:5',
                'rating_atmosphere' => 'nullable|integer|min:1|max:5',
                'rating_after'      => 'nullable|integer|min:1|max:5',
                'rating_quality'    => 'nullable|integer|min:1|max:5',
                'comment'           => 'required|string|max:2000',
                'purchased_car'     => 'nullable|string|max:255',
                'purchased_at'      => 'nullable|string|max:7',
                'guest_name'        => 'nullable|string|max:20',
                'guest_phone'       => 'nullable|string|max:11',
                'guest_email'       => 'nullable|email|max:100',
            ]);

            // ログイン済みの場合はmember_idを取得
            $memberId = null;
            try {
                $member   = auth('member')->user();
                $memberId = $member?->id;
            } catch (\Exception $e) {
                $memberId = null;
            }

            // 非ログイン時はゲスト情報が必須
            if (!$memberId) {
                $request->validate([
                    'guest_name'  => 'required|string|max:20',
                    'guest_phone' => 'required|string|max:11',
                    'guest_email' => 'required|email|max:100',
                ]);
            }

            $input = new StoreDealerReviewInputData(
                dealerId         : new DealerId($request->input('dealer_id')),
                nickname         : new Nickname($request->input('nickname')),
                rating           : (int) $request->input('rating'),
                ratingService    : $request->input('rating_service') ? (int) $request->input('rating_service') : null,
                ratingAtmosphere : $request->input('rating_atmosphere') ? (int) $request->input('rating_atmosphere') : null,
                ratingAfter      : $request->input('rating_after') ? (int) $request->input('rating_after') : null,
                ratingQuality    : $request->input('rating_quality') ? (int) $request->input('rating_quality') : null,
                comment          : new ReviewComment($request->input('comment')),
                purchasedCar     : $request->input('purchased_car') ? new PurchasedCar($request->input('purchased_car')) : null,
                purchasedAt      : $request->input('purchased_at') ? new PurchasedAt($request->input('purchased_at')) : null,
                memberId         : $memberId,
                guestName        : $request->input('guest_name') ? new GuestName($request->input('guest_name')) : null,
                guestPhone       : $request->input('guest_phone') ? new GuestPhone($request->input('guest_phone')) : null,
                guestEmail       : $request->input('guest_email') ? new GuestEmail($request->input('guest_email')) : null,
            );

            $output = $this->useCase->execute($input);

            return response()->json($output->toArray(), 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (Exception $e) {
            Log::error('StoreDealerReview error:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'サーバーエラーが発生しました。',
            ], 500);
        }
    }
}