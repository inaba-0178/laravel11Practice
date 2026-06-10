<?php

declare(strict_types=1);

namespace App\Domain\Shared\Constants;

final class AttachmentLimits
{
    // サイズ制限（バイト）
    public const IMAGE_MAX_SIZE = 10 * 1024 * 1024;   // 10MB
    public const VIDEO_MAX_SIZE = 100 * 1024 * 1024;  // 100MB
    public const FILE_MAX_SIZE  = 20 * 1024 * 1024;   // 20MB

    // 許可する拡張子
    public const IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    public const VIDEO_TYPES = ['mp4', 'mov'];
    public const FILE_TYPES  = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

    // 1回の送信最大ファイル数
    public const MAX_FILES = 5;

    // 全許可拡張子
    public static function allTypes(): array
    {
        return array_merge(self::IMAGE_TYPES, self::VIDEO_TYPES, self::FILE_TYPES);
    }

    // 拡張子からタイプを判定
    public static function getType(string $extension): string
    {
        $ext = strtolower($extension);
        if (in_array($ext, self::IMAGE_TYPES)) return 'image';
        if (in_array($ext, self::VIDEO_TYPES)) return 'video';
        return 'file';
    }

    // 拡張子からサイズ上限を取得
    public static function getMaxSize(string $extension): int
    {
        $type = self::getType($extension);
        return match($type) {
            'image' => self::IMAGE_MAX_SIZE,
            'video' => self::VIDEO_MAX_SIZE,
            default => self::FILE_MAX_SIZE,
        };
    }
}