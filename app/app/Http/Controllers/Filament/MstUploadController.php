<?php

declare(strict_types=1);

namespace App\Http\Controllers\Filament;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MstUploadController extends Controller
{
    /**
     * xlsxファイルアップロード
     */
    public function upload(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:51200',
        ]);

        $path = $request->file('file')->store('mst-uploads', 'local');

        return response()->json(['path' => $path]);
    }

    /**
     * 画像はLivewireのuploadImageChunk()でbase64として直接受け取るため
     * このコントローラーでのエンドポイントは不要。
     * （将来的にフォームPOSTで画像を受け取る場合に備えて残す）
     */
}