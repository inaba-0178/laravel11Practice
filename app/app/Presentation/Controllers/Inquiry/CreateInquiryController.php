<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Inquiry;

use App\Application\UseCases\Inquiry\CreateInquiryUseCase;
use App\Application\UseCases\Inquiry\CreateInquiryInputData;
use App\Domain\Inquiry\ValueObjects\DealerId;
use App\Domain\Inquiry\ValueObjects\CarId;
use App\Domain\Inquiry\ValueObjects\InquiryType;
use App\Domain\Inquiry\ValueObjects\InquiryName;
use App\Domain\Inquiry\ValueObjects\InquiryNickname;
use App\Domain\Inquiry\ValueObjects\InquiryEmail;
use App\Domain\Inquiry\ValueObjects\InquiryPhone;
use App\Domain\Inquiry\ValueObjects\InquiryPostalCode;
use App\Domain\Inquiry\ValueObjects\InquiryMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * 問い合わせ作成コントローラー
 *
 * HTTPリクエストの受付・バリデーション・レスポンス返却のみを担当する。
 * ビジネスロジックはCreateInquiryUseCaseに委譲する。
 *
 * POST /api/Inquiry/create
 */
class CreateInquiryController extends Controller
{
    public function __construct(
        private readonly CreateInquiryUseCase $useCase,
    ) {}

    /**
     * 問い合わせを作成する
     *
     * 処理の流れ：
     * 1. Laravelバリデーションで入力値の型・形式チェック
     * 2. ValueObjectでドメインルールのチェック
     * 3. メールor電話のどちらか必須チェック
     * 4. ユースケースに処理を委譲
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // Laravelバリデーション（型・形式チェック）
        $validated = $request->validate([
            'dealer_id'    => 'required|integer',
            'car_id'       => 'required|integer',
            'inquiry_type' => 'required|in:stock_check,estimate,condition_check,other',
            'name'         => 'nullable|string|max:100',
            'nickname'     => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:20',
            'postal_code'  => 'nullable|string|max:8',
            'address'      => 'nullable|string|max:255',
            'message'      => 'nullable|string',
        ]);

        // メールor電話のどちらか必須チェック（未ログイン時）
        $member = $request->user('members');
        if (!$member) {
            if (empty($validated['email']) && empty($validated['phone'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'メールアドレスまたは電話番号のどちらかを入力してください。',
                ], 422);
            }

            // 名前またはニックネームどちらか必須
            if (empty($validated['name']) && empty($validated['nickname'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'お名前またはニックネームのどちらかを入力してください。',
                ], 422);
            }

            if (empty($validated['postal_code'])) {
                return response()->json([
                    'success' => false,
                    'message' => '郵便番号を入力してください。',
                ], 422);
            }
        }

        try {
            $data = new CreateInquiryInputData(
                dealerId:    new DealerId($validated['dealer_id']),
                carId:       new CarId($validated['car_id']),
                inquiryType: new InquiryType($validated['inquiry_type']),
                memberId:    $member?->id,
                name:        isset($validated['name'])        ? new InquiryName($validated['name'])              : null,
                nickname:    isset($validated['nickname'])    ? new InquiryNickname($validated['nickname'])      : null,
                email:       isset($validated['email'])       ? new InquiryEmail($validated['email'])            : null,
                phone:       isset($validated['phone'])       ? new InquiryPhone($validated['phone'])            : null,
                postalCode:  isset($validated['postal_code']) ? new InquiryPostalCode($validated['postal_code']) : null,
                address:     $validated['address'] ?? null,
                message:     isset($validated['message'])     ? new InquiryMessage($validated['message'])        : null,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        // ユースケースに処理を委譲（保存・メール通知）
        $this->useCase->execute($data);

        return response()->json(['success' => true]);
    }
}