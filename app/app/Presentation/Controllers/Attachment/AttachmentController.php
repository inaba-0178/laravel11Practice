<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Attachment;

use App\Application\Services\AttachmentService;
use App\Domain\Shared\Constants\AttachmentLimits;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
    ) {}

    public function upload(Request $request, int $roomId): JsonResponse
    {
        $request->validate([
            'files'   => 'required|array|max:' . AttachmentLimits::MAX_FILES,
            'files.*' => 'file',
        ]);

        $results = [];
        foreach ($request->file('files') as $file) {
            $error = $this->attachmentService->validate($file);
            if ($error) {
                return response()->json(['message' => $error], 422);
            }
            $results[] = $this->attachmentService->upload($file, $roomId);
        }

        return response()->json($results);
    }
}