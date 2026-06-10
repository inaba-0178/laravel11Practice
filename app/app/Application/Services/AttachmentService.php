<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Shared\Constants\AttachmentLimits;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService
{
    public function upload(UploadedFile $file, int $roomId): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $type      = AttachmentLimits::getType($extension);
        $filename  = Str::uuid() . '.' . $extension;
        $path      = "chat/{$roomId}/{$filename}";

        // file_get_contents($file) → $file->get() に修正
        Storage::disk('s3')->put($path, $file->get(), 'public');

        return [
            'attachment_url'  => Storage::disk('s3')->url($path),
            'attachment_type' => $type,
            'attachment_name' => $file->getClientOriginalName(),
            'attachment_size' => $file->getSize(),
        ];
    }

    public function validate(UploadedFile $file): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        // 拡張子チェック
        if (!in_array($extension, AttachmentLimits::allTypes())) {
            return '対応していないファイル形式です';
        }

        // サイズチェック
        $maxSize = AttachmentLimits::getMaxSize($extension);
        if ($file->getSize() > $maxSize) {
            $maxMB = $maxSize / 1024 / 1024;
            return "ファイルサイズが上限（{$maxMB}MB）を超えています";
        }

        return null;
    }
}