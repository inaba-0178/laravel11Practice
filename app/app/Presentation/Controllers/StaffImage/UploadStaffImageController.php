<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\StaffImage;

use App\Application\UseCases\StaffImage\UploadStaffImageUseCase;
use App\Application\UseCases\StaffImage\UploadStaffImageInputData;
use App\Domain\StaffImage\ValueObjects\StaffId;
use App\Domain\StaffImage\Exceptions\StaffNotFoundException;
use App\Domain\Common\Constants\FileConstants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class UploadStaffImageController extends Controller
{
    public function __construct(
        private readonly UploadStaffImageUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'staff_id' => 'required|integer|min:1',
                'file'     => 'required|file|mimes:' . FileConstants::ALLOWED_IMAGE_MIMES . '|max:10240',
            ]);

            $input = new UploadStaffImageInputData(
                staffId : new StaffId($request->input('staff_id')),
                file    : $request->file('file'),
            );

            $output = $this->useCase->execute($input);

            return response()->json($output->toArray(), 201);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (StaffNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            Log::error('StaffImage upload error:', [
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