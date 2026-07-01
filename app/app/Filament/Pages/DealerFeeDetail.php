<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Http\Request;
use App\Filament\Resources\DealerFeeResource;
use App\Infrastructure\Eloquent\User\StkDealerFee;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class DealerFeeDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon           = 'heroicon-o-document-text';
    protected static string  $view                     = 'filament.pages.dealer-fee-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title                    = '';

    public ?string $id                  = null;
    public ?StkDealerFee $record        = null;

    public function mount(Request $request): void
    {
        $this->id     = $request->input('id');
        $this->record = StkDealerFee::whereNull('deleted_at')->findOrFail($this->id);

        if ($this->record->dealer_id !== Auth::user()->dealer_id) {
            abort(403);
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            DealerFeeResource::getUrl() => '諸費用管理',
            DealerFeeDetail::getUrl(['id' => $this->id]) => '詳細',
        ];
    }

    public function getTitle(): string
    {
        return '諸費用 : [' . $this->record->name . ']';
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'               => $this->record->id,
            'name'             => $this->record->name,
            'is_default'       => $this->record->is_default ? 'デフォルト' : '-',
            'registration_fee' => number_format((int) $this->record->registration_fee) . ' 円',
            'garage_cert_fee'  => number_format((int) $this->record->garage_cert_fee) . ' 円',
            'delivery_fee'     => number_format((int) $this->record->delivery_fee) . ' 円',
            'maintenance_fee'  => number_format((int) $this->record->maintenance_fee) . ' 円',
            'created_at'       => $this->record->created_at,
            'updated_at'       => $this->record->updated_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('諸費用情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('name')->label('プラン名'),
                        TextEntry::make('is_default')->label('デフォルト'),
                        TextEntry::make('registration_fee')->label('登録・手続き代行費用'),
                        TextEntry::make('garage_cert_fee')->label('車庫証明費用'),
                        TextEntry::make('delivery_fee')->label('納車費用'),
                        TextEntry::make('maintenance_fee')->label('整備費用'),
                        TextEntry::make('created_at')->label('作成日時'),
                        TextEntry::make('updated_at')->label('更新日時'),
                    ]),
            ]);
    }

    // 編集・削除
    protected function getHeaderActions(): array
    {
        return [
             Action::make('edit')
            ->label('編集')
            ->color('primary')
            ->url(DealerFeeResource::getUrl('edit', ['record' => $this->record->id])),

            Action::make('delete')
                ->label('削除')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('諸費用プランを削除しますか？')
                ->modalDescription('削除すると一覧から表示されなくなります。')
                ->modalSubmitActionLabel('削除する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () {
                    $this->record->delete();

                    Notification::make()
                        ->title('削除しました')
                        ->success()
                        ->send();

                    $this->redirect(DealerFeeResource::getUrl('index'));
                }),
        ];
    }
}