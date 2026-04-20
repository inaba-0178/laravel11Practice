<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;

class ChangePassword extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = NavigationGroup::USER_GROUP->value;
    protected static ?string $navigationIcon  = 'heroicon-o-lock-closed';
    protected static string  $view            = 'filament.pages.change-password';
    protected static ?string $title           = 'パスワード変更';
    protected static ?string $navigationLabel = 'パスワード変更';
    protected static ?int    $navigationSort  = NavigationSort::USER_PASSWORD->value;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('現在のパスワード')
                    ->password()
                    ->revealable()
                    ->required(),

                TextInput::make('new_password')
                    ->label('新しいパスワード')
                    ->password()
                    ->required()
                    ->revealable()
                    ->minLength(10)
                    ->rules([
                        'regex:/^[a-z0-9!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]*$/',
                    ])
                    ->validationMessages([
                        'min'   => 'パスワードは、英数小文字、記号で10文字以上で入力してください。',
                        'regex' => 'パスワードに、英数小文字、記号以外が含まれています。',
                    ]),

                TextInput::make('new_password_confirmation')
                    ->label('新しいパスワード（確認）')
                    ->password()
                    ->required()
                    ->revealable()
                    ->same('new_password')
                    ->validationMessages([
                        'same' => '新しいパスワードと一致しません。',
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        // 現在のパスワード確認
        if (!Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->title('現在のパスワードが正しくありません')
                ->danger()
                ->send();
            return;
        }

        // 新しいパスワードが現在と同じ場合
        if (Hash::check($data['new_password'], $user->password)) {
            Notification::make()
                ->title('新しいパスワードは現在のパスワードと異なるものを設定してください')
                ->danger()
                ->send();
            return;
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        $this->form->fill();

        Notification::make()
            ->title('パスワードを変更しました')
            ->success()
            ->send();
    }
}