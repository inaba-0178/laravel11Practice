<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatResource\Pages;

use App\Filament\Resources\ChatResource;
use App\Infrastructure\Eloquent\User\Room;
use App\Infrastructure\Eloquent\User\UsrUser;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListChats extends ListRecords
{
    protected static string $resource = ChatResource::class;

    public function getTitle(): string
    {
        return 'チャット一覧';
    }

    protected function getHeaderActions(): array
    {
        $role = Auth::user()?->role;
        $canCreate = in_array($role, ['dealer', 'dealer_staff']);

        if (!$canCreate) {
            return [];
        }

        return [
            Action::make('create_room')
                ->label('新規ルーム作成')
                ->color('primary')
                ->icon('heroicon-o-plus')
                ->form([
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
                        ->label('紐づくタイプ')
                        ->options([
                            'inquiry'          => '問い合わせ',
                            'car_qa'           => '車両Q&A',
                            'dealer_internal'  => 'ディーラー内部',
                        ])
                        ->nullable(),

                    TextInput::make('related_id')
                        ->label('紐づくID')
                        ->numeric()
                        ->nullable(),

                    Repeater::make('users')
                        ->label('参加ユーザー')
                        ->schema([
                            Select::make('user_type')
                                ->label('ユーザータイプ')
                                ->options([
                                    'staff'  => 'ディーラー・スタッフ',
                                    'member' => 'サイトユーザー',
                                ])
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn ($set) => $set('user_id', null)),

                            Select::make('user_id')
                                ->label('ユーザー')
                                ->options(function ($get) {
                                    $userType = $get('user_type');
                                    if ($userType === 'staff') {
                                        return User::where('is_active', 1)
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    }
                                    if ($userType === 'member') {
                                        return UsrUser::orderBy('sei')
                                            ->get()
                                            ->mapWithKeys(fn ($u) => [
                                                $u->id => $u->sei . ' ' . $u->mei . '（' . $u->email . '）'
                                            ])
                                            ->toArray();
                                    }
                                    return [];
                                })
                                ->required()
                                ->searchable(),
                        ])
                        ->addActionLabel('＋ ユーザーを追加')
                        ->minItems(1)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    $room = Room::create([
                        'name'         => $data['name'] ?? null,
                        'type'         => $data['type'],
                        'related_type' => $data['related_type'] ?? null,
                        'related_id'   => $data['related_id'] ?? null,
                        'is_active'    => 1,
                    ]);

                    // 作成者（ログイン中のスタッフ）を追加
                    $room->roomUsers()->create([
                        'user_id'   => (string) Auth::id(),
                        'user_type' => 'staff',
                    ]);

                    // 選択したユーザーを追加
                    foreach ($data['users'] ?? [] as $user) {
                        if (empty($user['user_id'])) continue;

                        // 重複チェック
                        $exists = $room->roomUsers()
                            ->where('user_id', $user['user_id'])
                            ->where('user_type', $user['user_type'])
                            ->exists();

                        if (!$exists) {
                            $room->roomUsers()->create([
                                'user_id'   => $user['user_id'],
                                'user_type' => $user['user_type'],
                            ]);
                        }
                    }

                    Notification::make()
                        ->title('ルームを作成しました')
                        ->success()
                        ->send();

                    $this->redirect(ViewChat::getUrl(['record' => $room]));
                }),
        ];
    }
}