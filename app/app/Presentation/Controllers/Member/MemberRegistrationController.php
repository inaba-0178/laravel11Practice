<?php

namespace App\Presentation\Controllers\Member;

use App\Application\UseCases\Member\ProvisionalRegistrationUseCase;
use App\Application\UseCases\Member\RegisterUseCase;
use App\Domain\Member\ValueObjects\RegisterEmail;
use App\Domain\Member\ValueObjects\RegisterRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MemberRegistrationController extends Controller
{
    public function __construct(
        private readonly ProvisionalRegistrationUseCase $provisionalRegistrationUseCase,
        private readonly RegisterUseCase                $registerUseCase,
    ) {}

    // 仮登録
    public function provisional(Request $request): JsonResponse
    {
        try {
            $email = new RegisterEmail(email: $request->email ?? '');
            $this->provisionalRegistrationUseCase->execute($email);
            return response()->json(['message' => '確認メールを送信しました']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'エラーが発生しました'], 500);
        }
    }

    // 本登録
    public function register(Request $request): JsonResponse
    {
        try {
            $registerRequest = new RegisterRequest(
                sei:           $request->sei ?? '',
                mei:           $request->mei ?? '',
                sei_kana:      $request->sei_kana ?? '',
                mei_kana:      $request->mei_kana ?? '',
                birth_date:    $request->birth_date ?? '',
                post_code:     $request->post_code ?? '',
                prefecture:    $request->prefecture ?? '',
                city:          $request->city ?? '',
                address_line1: $request->address_line1 ?? '',
                address_line2: $request->address_line2 ?? null,
                phone_number:  $request->phone_number ?? '',
                gender:        (int) ($request->gender ?? 0),
                email:         $request->email ?? '',
                token:         $request->token ?? '',
                password:      $request->password ?? '',
            );
            $this->registerUseCase->execute($registerRequest);
            return response()->json(['message' => '会員登録が完了しました']);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'field'   => $this->resolveField($e->getMessage()),
                'message' => $e->getMessage(),
            ], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'エラーが発生しました'], 500);
        }
    }

    private function resolveField(string $message): string
    {
        return match(true) {
            str_contains($message, '苗字')     => 'sei',
            str_contains($message, '名前')     => 'mei',
            str_contains($message, '苗字カナ') => 'sei_kana',
            str_contains($message, '名前カナ') => 'mei_kana',
            str_contains($message, '生年月日') => 'birth_date',
            str_contains($message, '郵便番号') => 'post_code',
            str_contains($message, '都道府県') => 'prefecture',
            str_contains($message, '市区町村') => 'city',
            str_contains($message, '番地')     => 'address_line1',
            str_contains($message, '建物名')   => 'address_line2',
            str_contains($message, '電話番号') => 'phone_number',
            str_contains($message, '性別')     => 'gender',
            str_contains($message, 'パスワード') => 'password',
            str_contains($message, 'メールアドレス') => 'email',
            default                            => 'global',
        };
    }
}