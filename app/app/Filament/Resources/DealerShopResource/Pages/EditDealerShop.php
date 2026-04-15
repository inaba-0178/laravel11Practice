<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerShopResource\Pages;

use App\Filament\Resources\DealerShopResource;
use App\Filament\Pages\DealerShopDetail;
use App\Filament\Resources\DealerShopResource\Concerns\ValidatesDealerShopData;
use App\Infrastructure\Eloquent\User\StkDealerImage;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

class EditDealerShop extends EditRecord
{
    use ValidatesDealerShopData;

    protected static string $resource = DealerShopResource::class;
    protected static string $view     = 'filament.pages.dealer-shop-edit';

    protected function getRedirectUrl(): string
    {
        return DealerShopDetail::getUrl(['id' => $this->record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveWithConfirm')
                ->label('保存する')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('店舗情報を保存しますか？')
                ->modalDescription(function () {
                    return new HtmlString('
                        <div>
                            <p style="font-size:13px; color:#374151; margin:0 0 8px;">入力内容を保存します。</p>
                            <div id="upload-warning" style="display:none; margin-top:12px; padding:12px; background:#fef3c7; border:1px solid #f59e0b; border-radius:8px;">
                                <p style="color:#92400e; font-size:13px; margin:0 0 4px; font-weight:500;">⚠️ 画像がアップロードされていません</p>
                                <p style="color:#92400e; font-size:12px; margin:0;">ファイルが選択されていますがアップロードボタンを押下でアップロードされていません。</p>
                                <p style="color:#92400e; font-size:12px; margin:4px 0 0;">画像アップロードボタンを押下でアップロードされます。</p>
                            </div>
                        </div>
                        <script>
                            setTimeout(() => {
                                if (window.dealerImageFileCount > 0) {
                                    document.getElementById("upload-warning").style.display = "block";
                                }
                            }, 100);
                        </script>
                    ');
                })
                ->modalSubmitActionLabel('保存する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () {
                    try {
                        $data = $this->form->getState();
                        $this->validateDealerShopData($data);
                        $this->record->update($data);

                        Notification::make()
                            ->title('保存しました')
                            ->success()
                            ->send();

                        $this->redirect(DealerShopDetail::getUrl(['id' => $this->record->id]));

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('エラーが発生しました')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }
}