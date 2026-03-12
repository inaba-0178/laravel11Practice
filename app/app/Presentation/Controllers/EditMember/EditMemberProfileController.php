<?php

namespace App\Presentation\Controllers\EditMember;

use App\Application\UseCases\EditMember\GetProfileUseCase;
use App\Application\UseCases\EditMember\UpdateProfileUseCase;
use App\Domain\EditMember\ValueObjects\UpdateProfileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EditMemberProfileController extends Controller
{
    public function __construct(
        private readonly GetProfileUseCase    $getProfileUseCase,
        private readonly UpdateProfileUseCase $updateProfileUseCase,
    ) {}

    public function show(Request $request): JsonResponse
    {
        try {
            $output = $this->getProfileUseCase->execute($request->user()->id);
            return response()->json($output->toArray());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'エラーが発生しました'], 500);
        }
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $updateRequest = new UpdateProfileRequest(
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
            );

            $member = $this->updateProfileUseCase->execute($request->user()->id, $updateRequest);
            return response()->json($member->toArray());
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
            str_contains($message, '苗字カナ') => 'sei_kana',
            str_contains($message, '名前カナ') => 'mei_kana',
            str_contains($message, '苗字')     => 'sei',
            str_contains($message, '名前')     => 'mei',
            str_contains($message, '生年月日') => 'birth_date',
            str_contains($message, '郵便番号') => 'post_code',
            str_contains($message, '都道府県') => 'prefecture',
            str_contains($message, '市区町村') => 'city',
            str_contains($message, '番地')     => 'address_line1',
            str_contains($message, '建物名')   => 'address_line2',
            str_contains($message, '電話番号') => 'phone_number',
            str_contains($message, '性別')     => 'gender',
            default                            => 'global',
        };
    }
}