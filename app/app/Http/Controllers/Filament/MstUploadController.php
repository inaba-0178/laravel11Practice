<?php

declare(strict_types=1);

namespace App\Http\Controllers\Filament;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MstUploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        \Log::info('upload request', [
            'hasFile' => $request->hasFile('file'),
            'files'   => $request->allFiles(),
        ]);

        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:51200',
        ]);

        $path = $request->file('file')->store('mst-uploads', 'local');

        \Log::info('upload path', ['path' => $path]);

        return response()->json(['path' => $path]);
    }
}