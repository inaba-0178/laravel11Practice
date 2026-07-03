<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\NavigationGroup;
use App\Constants\RoleConstants;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class OprErrorLogPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-exclamation-circle';
    protected static string  $view            = 'filament.pages.opr-error-log';
    protected static ?string $navigationGroup = NavigationGroup::SYSTEM_GROUP->value;
    protected static ?string $title           = 'エラーログ';
    protected static ?int    $navigationSort  = 906;

    public static function canAccess(): bool
    {
        return auth()->user()?->role === RoleConstants::SUPER;
    }

    public ?string $filterLevel   = 'ERROR';
    public ?string $filterKeyword = null;

    private const READ_BYTES  = 512 * 1024;
    private const MAX_ENTRIES = 200;

    public function getHeaderActions(): array
    {
        return [
            Action::make('clearLog')
                ->label('ログクリア')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('ログファイルをクリア')
                ->modalDescription('ログファイルを空にします。この操作は取り消せません。')
                ->action(function (): void {
                    file_put_contents(storage_path('logs/laravel.log'), '');
                    Notification::make()->title('ログをクリアしました')->success()->send();
                }),
        ];
    }

    public function getLogEntries(): array
    {
        $path = storage_path('logs/laravel.log');
        if (!file_exists($path) || filesize($path) === 0) return [];

        $size      = filesize($path);
        $readBytes = min($size, self::READ_BYTES);
        $fh        = fopen($path, 'r');
        fseek($fh, $size - $readBytes);
        $content = fread($fh, $readBytes);
        fclose($fh);

        // 先頭の不完全なエントリを除去
        $first = strpos($content, "\n[");
        if ($first !== false) {
            $content = substr($content, $first + 1);
        }

        preg_match_all(
            '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+?)(?=^\[|\z)/ms',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        $entries  = [];
        $level    = strtoupper($this->filterLevel ?? '');
        $keyword  = $this->filterKeyword;

        foreach (array_reverse($matches) as $m) {
            $entryLevel = strtoupper($m[3]);
            $body       = rtrim($m[4]);

            if ($level && $entryLevel !== $level) continue;
            if ($keyword && !str_contains($body, $keyword)) continue;

            // メッセージ行とスタックトレース部分を分離
            $nlPos   = strpos($body, "\n");
            $firstLine = $nlPos !== false ? substr($body, 0, $nlPos) : $body;
            $detail    = $nlPos !== false ? trim(substr($body, $nlPos)) : null;

            // メッセージ行から JSON コンテキスト部分を分離
            $jsonPos = strpos($firstLine, ' {"');
            $message = $jsonPos !== false ? substr($firstLine, 0, $jsonPos) : $firstLine;
            $inline  = $jsonPos !== false ? substr($firstLine, $jsonPos + 1) : null;

            $entries[] = [
                'at'      => $m[1],
                'env'     => $m[2],
                'level'   => $entryLevel,
                'message' => $message,
                'detail'  => implode("\n", array_filter([$inline, $detail])) ?: null,
            ];

            if (count($entries) >= self::MAX_ENTRIES) break;
        }

        return $entries;
    }

    public function getLogFileInfo(): array
    {
        $path = storage_path('logs/laravel.log');
        if (!file_exists($path)) {
            return ['exists' => false];
        }
        $bytes = filesize($path);
        return [
            'exists'   => true,
            'size'     => $this->formatBytes($bytes),
            'modified' => date('Y/m/d H:i:s', filemtime($path)),
        ];
    }

    public static function levelColor(string $level): string
    {
        return match ($level) {
            'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY' => 'danger',
            'WARNING'                                   => 'warning',
            'NOTICE', 'INFO'                            => 'info',
            default                                     => 'gray',
        };
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) return round($bytes / 1024 / 1024, 1) . ' MB';
        if ($bytes >= 1024)        return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
