<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\EmailChange;

use App\Application\UseCases\EmailChange\UpdateEmailUseCase;
use App\Domain\EmailChange\ValueObjects\ChangeEmailRequest;
use App\Domain\EmailChange\ValueObjects\CurrentEmailRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class EmailChangeController extends Controller
{
    public function __construct(
        private readonly UpdateEmailUseCase $updateEmailUseCase,
    ) {}

    public function update(Request $request): JsonResponse
    {
        try {
            $member = Auth::guard('member')->user();

            $currentEmailRequest = new CurrentEmailRequest(
                currentEmail: $request->input('current_email', ''),
            );

            $vo = new ChangeEmailRequest(
                usrUserId:    $member->id,
                newEmail:     $request->input('new_email', ''),
                currentEmail: $member->email,
            );

            $this->updateEmailUseCase->execute($vo, $currentEmailRequest);

            return response()->json([
                'message' => 'メールアドレスを変更しました。',
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        } catch (Throwable $e) {
            return response()->json(['message' => 'メールアドレスの変更に失敗しました。'], 500);
        }
    }
}