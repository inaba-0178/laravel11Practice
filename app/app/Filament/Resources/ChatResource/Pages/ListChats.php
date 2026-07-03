<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatResource\Pages;

use App\Application\Services\MailService;
use App\Domain\Shared\Constants\MailTemplateKey;
use App\Domain\Shared\Constants\UserType;
use App\Filament\Resources\ChatResource;
use App\Infrastructure\Eloquent\User\Room;
use App\Infrastructure\Eloquent\User\UsrUser;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListChats extends ListRecords
{
    protected static string $resource = ChatResource::class;

    private const CREATABLE_ROLES       = ['dealer', 'dealer_staff'];
    private const DEFAULT_EXPIRE_HOURS  = 72;

    public function getTitle(): string
    {
        return 'チャット一覧';
    }

    protected function getHeaderActions(): array
    {
        if (!in_array(Auth::user()?->role, self::CREATABLE_ROLES)) {
            return [];
        }

        return [
            Action::make('create_room')
                ->label('新規ルーム作成')
                ->color('primary')
                ->icon('heroicon-o-plus')
                ->form($this->createRoomForm())
                ->action(fn (array $data) => $this->createRoom($data)),
        ];
    }

    private function createRoomForm(): array
    {
        return [
            TextInput::make('name')
                ->label('ルーム名')
                ->nullable()
                ->maxLength(255),

            Select::make('type')
                ->label('ルームタイプ')
                ->options([
                    'direct' => 'ダイレクト',
                    'group'  => 'グループ',
                ])
                ->required()
                ->default('direct'),

            Select::make('related_type')
                ->label('チャット種別')
                ->options([
                    'inquiry'         => '問い合わせ',
                    'car_qa'          => '車両Q&A',
                    'dealer_internal' => 'ディーラー内部',
                ])
                ->nullable(),

            Repeater::make('members')
                ->label('招待するユーザー')
                ->schema([
                    TextInput::make('email')
                        ->label('メールアドレス')
                        ->email()
                        ->required()
                        ->placeholder('招待するユーザーのメールアドレスを入力'),

                    Placeholder::make('notice')
                        ->label('')
                        ->content('※入力されたメールアドレスに招待メールを送信します。'),
                ])
                ->addActionLabel('＋ ユーザーを追加')
                ->minItems(1)
                ->columnSpanFull(),

            Repeater::make('staff_users')
                ->label('参加するディーラー担当者')
                ->schema([
                    Select::make('user_id')
                        ->label('担当者')
                        ->options(fn () => User::where('is_active', 1)
                            ->where('dealer_id', Auth::user()->getEffectiveDealerId())
                            ->where('id', '!=', Auth::id())
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                        )
                        ->searchable()
                        ->nullable(),
                ])
                ->addActionLabel('＋ 担当者を追加')
                ->minItems(0)
                ->columnSpanFull(),
        ];
    }

    private function createRoom(array $data): void
    {
        $room = Room::create([
            'name'         => $data['name'] ?? null,
            'type'         => $data['type'],
            'related_type' => $data['related_type'] ?? null,
            'related_id'   => $this->generateRelatedId(),
            'is_active'    => 1,
        ]);

        // 作成者を追加
        $this->attachStaff($room, (string) Auth::id());

        // 追加担当者を追加
        foreach ($data['staff_users'] ?? [] as $staff) {
            if (empty($staff['user_id'])) continue;
            $this->attachStaff($room, (string) $staff['user_id']);
        }

        // ユーザーを招待
        $expireHours = $this->getExpireHours();
        foreach ($data['members'] ?? [] as $member) {
            if (empty($member['email'])) continue;
            $this->inviteMember($room, $member['email'], $expireHours);
        }

        Notification::make()->title('ルームを作成しました')->success()->send();
        $this->redirect(ViewChat::getUrl(['record' => $room]));
    }

    private function attachStaff(Room $room, string $userId): void
    {
        $exists = $room->roomUsers()
            ->where('user_id', $userId)
            ->where('user_type', UserType::STAFF)
            ->exists();

        if ($exists) return;

        $room->roomUsers()->create([
            'user_id'      => $userId,
            'user_type'    => UserType::STAFF,
            'status'       => 'approved',
            'invited_at'   => now(),
            'responded_at' => now(),
        ]);
    }

    private function inviteMember(Room $room, string $email, int $expireHours): void
    {
        $user = UsrUser::where('email', $email)->first();
        if (!$user) return;

        $exists = $room->roomUsers()
            ->where('user_id', $user->id)
            ->where('user_type', UserType::MEMBER)
            ->exists();

        if (!$exists) {
            $room->roomUsers()->create([
                'user_id'    => $user->id,
                'user_type'  => UserType::MEMBER,
                'status'     => 'pending',
                'invited_at' => now(),
                'expired_at' => now()->addHours($expireHours),
            ]);

            app(MailService::class)->send(
                templateKey:  MailTemplateKey::CHAT_INVITED,
                toEmail:      $email,
                placeholders: [
                    'user_name'   => $user->display_name,
                    'dealer_name' => Auth::user()->name,
                    'url'         => config('app.frontend_url') . '/chat/invite?room=' . $room->id,
                ],
            );
        }
    }

    private function getExpireHours(): int
    {
        return (int) DB::connection('mst')
            ->table('opr_settings')
            ->where('key', 'chat_invite_expire_hours')
            ->value('value') ?? self::DEFAULT_EXPIRE_HOURS;
    }

    private function generateRelatedId(): string
    {
        $dealerId = Auth::user()->getEffectiveDealerId();

        do {
            $relatedId = $dealerId . '-' . Str::random(8);
        } while (Room::where('related_id', $relatedId)->exists());

        return $relatedId;
    }
}