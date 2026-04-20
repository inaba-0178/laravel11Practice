<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Constants\RoleConstants;
use App\Application\Services\MailService;
use App\Domain\Shared\Constants\MailTemplateKey;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if (in_array($user->role, [
            RoleConstants::DEALER,
            RoleConstants::DEALER_STAFF,
        ])) {
            $data['dealer_id'] = $user->dealer_id;
        }

        // ランダムパスワード生成
        $rawPassword      = Str::password(12, true, true, true, false);
        $data['password'] = bcrypt($rawPassword);

        // メール送信用に一時保存
        $this->rawPassword = $rawPassword;

        return $data;
    }

    private string $rawPassword = '';

    protected function afterCreate(): void
    {
        try {
            app(MailService::class)->send(
                templateKey:  MailTemplateKey::USER_CREATED,
                toEmail:      $this->record->email,
                placeholders: [
                    'name'      => $this->record->name,
                    'email'     => $this->record->email,
                    'password'  => $this->rawPassword,
                    'login_url' => config('app.admin_url') . '/login',
                ],
            );

            Notification::make()
                ->title('ユーザーを作成しました')
                ->body('初期パスワードをメールで送信しました。')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('ユーザーは作成されましたがメール送信に失敗しました')
                ->body($e->getMessage())
                ->warning()
                ->send();
        }
    }
}