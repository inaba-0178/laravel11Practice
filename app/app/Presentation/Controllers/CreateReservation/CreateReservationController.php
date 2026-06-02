<?php

namespace App\Presentation\Controllers\CreateReservation;

use App\Application\UseCases\CreateReservation\CreateReservationUseCase;
use App\Domain\CreateReservation\Exceptions\ScheduleFullException;
use App\Domain\CreateReservation\ValueObjects\ReservationData;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Domain\CreateReservation\Exceptions\PastTimeException;
use App\Infrastructure\Eloquent\User\UsrUser;

class CreateReservationController extends Controller
{
    public function __construct(
        private readonly CreateReservationUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'dealer_id'           => 'required|integer|min:1',
                'car_id'              => 'required|integer|min:1',
                'reservation_type_id' => 'required|integer|min:1',
                'schedule_id'         => 'required|integer|min:1',
                'max_reservations'    => 'required|integer|min:1',
                'memo'                => 'nullable|string|max:1000',
                'agreed_terms'        => 'required|accepted',
                'guest_name'          => 'nullable|string|max:100',
                'guest_phone'         => 'nullable|string|max:20',
                'guest_email'         => 'nullable|email|max:255',
                'guest_address'       => 'nullable|string|max:255',
            ]);

            $memberId = Auth::guard('sanctum')->id();

            // ゲストの場合：電話番号かメールアドレスのどちらかが必須
            if (!$memberId) {
                if (empty($validated['guest_phone']) && empty($validated['guest_email'])) {
                    return response()->json([
                        'success' => false,
                        'message' => '電話番号またはメールアドレスを入力してください。',
                    ], 422);
                }
            }

            // 会員の場合はUsrUserから情報を取得
            $guestName    = $validated['guest_name'] ?? null;
            $guestPhone   = $validated['guest_phone'] ?? null;
            $guestEmail   = $validated['guest_email'] ?? null;
            $guestAddress = $validated['guest_address'] ?? null;

            if ($memberId) {
                $user = UsrUser::find($memberId);
                if ($user) {
                    $guestName    = $user->full_name;
                    $guestPhone   = $user->phone_number;
                    $guestEmail   = $user->email;
                    $guestAddress = $user->prefecture . $user->city . $user->address_line1;
                }
            }


            $reservationData = new ReservationData(
                dealerId          : (int)$validated['dealer_id'],
                carId             : (int)$validated['car_id'],
                memberId          : $memberId,
                reservationTypeId : (int)$validated['reservation_type_id'],
                scheduleId        : (int)$validated['schedule_id'],
                maxReservations   : (int)$validated['max_reservations'],
                memo              : $validated['memo'] ?? null,
                guestName         : $guestName,
                guestPhone        : $guestPhone,
                guestEmail        : $guestEmail,
                guestAddress      : $guestAddress,
            );

            $outputData = $this->useCase->execute($reservationData);

            return response()->json($outputData->toArray(), 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);

        } catch (ScheduleFullException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        } catch (PastTimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        } catch (Exception $e) {
            Log::error('CreateReservation error:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => '大変申し訳ございません。予約できませんでした。',
            ], 500);
        }
    }
}