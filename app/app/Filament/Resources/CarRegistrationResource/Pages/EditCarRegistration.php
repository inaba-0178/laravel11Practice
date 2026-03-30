<?php

declare(strict_types=1);

namespace App\Filament\Resources\CarRegistrationResource\Pages;

use App\Application\Services\MailService;
use App\Domain\Shared\Constants\MailTemplateKey;
use App\Filament\Resources\CarRegistrationResource;
use App\Infrastructure\Eloquent\User\StkCarImages;
use App\Infrastructure\Eloquent\User\StkCarOptions;

use App\Infrastructure\Eloquent\Mst\MstEquipmentSafety;
use App\Infrastructure\Eloquent\Mst\MstEquipmentBasic;
use App\Infrastructure\Eloquent\Mst\MstSeatOption;
use App\Infrastructure\Eloquent\Mst\MstEquipmentDressup;
use App\Infrastructure\Eloquent\Mst\MstEquipmentEnv;

use App\Notifications\CarRegistrationPendingNotification;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Constants\SpecialTypeOption;
use App\Constants\CarStatus;

class EditCarRegistration extends EditRecord
{
    protected static string $resource = CarRegistrationResource::class;

    // デフォルトのフッターボタンを非表示
    protected function getFormActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        $record    = $this->getRecord();
        $hasImages = StkCarImages::where('car_id', $record->id)->exists();
        $actions   = [];

        // ===== 差し戻し理由表示ボタン（rejectedのみ） =====
        if ($record->status === 'rejected' && $record->rejection_reason) {
            $actions[] = Action::make('rejection_notice')
                ->label('⚠️ 差し戻し理由を確認する')
                ->color('danger')
                ->modalHeading('差し戻し理由')
                ->modalContent(fn () => view('filament.modals.rejection-reason', [
                    'reason' => $record->rejection_reason,
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('閉じる');
        }

        // ===== 承認依頼ボタン =====
        if (in_array($record->status, CarStatus::CAN_REQUEST_APPROVAL)) {
            $actions[] = Action::make('request_approval')
                ->label('承認依頼')
                ->color('primary')
                ->disabled(!$hasImages)
                ->tooltip(!$hasImages ? '画像を1枚以上アップロードしてください' : null)
                ->requiresConfirmation()
                ->modalHeading('承認依頼を管理者に送ります')
                ->modalDescription('入力内容や画像アップロードに問題ありませんか？')
                ->modalSubmitActionLabel('承認依頼')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () use ($record) {
                    DB::transaction(function () use ($record) {
                        $record->update([
                            'status'           => 'pending',
                            'rejection_reason' => null,
                        ]);
                    });

                    $seriesName = $record->series?->series_name ?? '不明';
                    $dealerName = $record->dealer?->name ?? '不明';

                    User::whereIn('role', ['super', 'admin'])
                        ->where('is_active', 1)
                        ->get()
                        ->each(fn (User $u) => $u->notify(
                            new CarRegistrationPendingNotification($seriesName, $dealerName, $record->id)
                        ));

                    // 同ディーラーユーザーにも通知
                    User::where('dealer_id', $record->dealer_id)
                        ->where('is_active', 1)
                        ->get()
                        ->each(fn (User $u) => $u->notify(
                            new \App\Notifications\CarRegistrationStatusNotification(
                                carName: $seriesName,
                                status:  'pending',
                                message: '承認依頼を送信しました。管理者の承認をお待ちください。',
                                carId:   $record->id,
                            )
                        ));

                    Notification::make()
                        ->title('承認依頼を管理者に送りました。承認されるまでには時間がかかりますのでお待ちください。')
                        ->success()
                        ->send();
                });
        }

        // ===== 画像アップロードボタン =====
        $actions[] = Action::make('upload_images')
            ->label('画像アップロード')
            ->color('info')
            ->modalHeading('')
            ->modalContent(fn () => view('filament.modals.car-image-modal', [
                'carId' => $record->id,
            ]))
            ->modalSubmitActionLabel('確定')
            ->modalCancelActionLabel('キャンセル')
            ->modalWidth('4xl')
            ->action(function () {
                // 確定ボタンはモーダルを閉じるだけ
                // 画像の保存はLivewire側で随時実行済み
            });


        // ===== 保存ボタン =====
        $actions[] = Action::make('save')
            ->label('保存')
            ->color('success')
            ->action(function () use ($record) {
                $this->save();

                $this->replaceMountedAction('save_complete');
            });

        // 保存完了モーダル（内部用）
        $actions[] = Action::make('save_complete')
            ->label('保存完了')
            ->modalHeading('一時保存しました')
            ->modalContent(view('filament.modals.save-complete'))
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->extraModalFooterActions([
                Action::make('go_to_list')
                    ->label('一覧に戻る')
                    ->color('primary')
                    ->url($this->getResource()::getUrl('index')),
                Action::make('close_modal')
                    ->label('閉じる')
                    ->color('gray')
                    ->cancelParentActions(),
            ])
            ->visible(false);

        // ===== 削除ボタン（pending/rejectedのみ） =====
        if (in_array($record->status, CarStatus::CAN_DELETE)) {
            $actions[] = Action::make('delete_car')
                ->label('削除')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('この車両を削除しますか？')
                ->modalDescription('削除すると承認依頼も取り下げられます。この操作は取り消せません。')
                ->modalSubmitActionLabel('削除する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () use ($record) {
                    $record->update(['status' => 'deleted']);
                    $record->delete();

                    Notification::make()
                        ->title('車両を削除しました')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                });
        }

        return $actions;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $detail = $this->getRecord()->detail;

        if ($detail) {
            $data['first_registration_date'] = $detail->first_registration_date;
            $data['inspection_expire_date']  = $detail->inspection_expire_date;
            $data['inspection_status']       = $detail->inspection_status;
            $data['drive_system']            = $detail->drive_system;
            $data['displacement']            = $detail->displacement;
            $data['steering_wheel']          = $detail->steering_wheel;
            $data['number_of_doors']         = $detail->number_of_doors;
            $data['slide_door']              = $detail->slide_door;
            $data['riding_capacity']         = $detail->riding_capacity;
            $data['loan_available']          = $detail->loan_available;
            $data['description']             = $detail->description;
        }
        // special_typeオプションの読み込み
        $specialOptions = $this->getRecord()->options
            ->where('option_category', 'special_type')
            ->pluck('option_name')
            ->toArray();

        $data['special_one_owner']   = in_array('one_owner',   $specialOptions);
        $data['special_camping_car'] = in_array('camping_car', $specialOptions);
        $data['special_welfare_car'] = in_array('welfare_car', $specialOptions);
        $data['special_unused']      = in_array('unused',      $specialOptions);
        $data['special_eco_car']     = in_array('eco_car',     $specialOptions);

        // 装備仕様オプションの読み込み
        $equipmentCategories = ['safety', 'basic', 'seat', 'dress_up', 'environmental'];

        $equippedOptions = $this->getRecord()->options
            ->whereIn('option_category', $equipmentCategories)
            ->pluck('option_name')
            ->toArray();

        // マスタから全装備を取得してチェック状態を復元
        $equipmentMap = [
            'safety'        => MstEquipmentSafety::where('is_active', 1)->get(),
            'basic'         => MstEquipmentBasic::where('is_active', 1)->get(),
            'seat'          => MstSeatOption::where('is_active', 1)->get(),
            'dress_up'      => MstEquipmentDressup::where('is_active', 1)->get(),
            'environmental' => MstEquipmentEnv::where('is_active', 1)->get(),
        ];

        foreach ($equipmentMap as $category => $items) {
            foreach ($items as $item) {
                $key          = "equipment_{$category}_{$item->value}";
                $data[$key]   = in_array($item->value, $equippedOptions);
            }
        }

        // その他オプションの読み込み
        $otherCategories = ['audio', 'navigation', 'special_type', 'other'];

        $otherOptions = $this->getRecord()->options
            ->whereIn('option_category', $otherCategories)
            ->filter(fn ($option) => !in_array(
                $option->option_name,
                SpecialTypeOption::EXCLUDE_FROM_OTHER_OPTIONS
            ))
            ->map(fn ($option) => [
                'option_category' => $option->option_category,
                'option_name'     => $option->option_name,
            ])
            ->values()
            ->toArray();

        $data['other_options'] = $otherOptions;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        // available編集時はpendingに戻す
        if ($record->status === 'available') {
            $data['status']           = 'pending';
            $data['rejection_reason'] = null;
        }

        // stk_car_detailsを更新
        $record->detail()->updateOrCreate(
            ['car_id' => $record->id],
            [
                'first_registration_date' => $data['first_registration_date'] ?? null,
                'inspection_expire_date'  => $data['inspection_expire_date'] ?? null,
                'inspection_status'       => $data['inspection_status'] ?? 'available',
                'drive_system'            => $data['drive_system'] ?? null,
                'displacement'            => $data['displacement'] ?? 0,
                'steering_wheel'          => $data['steering_wheel'] ?? 'right',
                'number_of_doors'         => $data['number_of_doors'] ?? 0,
                'slide_door'              => $data['slide_door'] ?? 'none',
                'riding_capacity'         => $data['riding_capacity'] ?? null,
                'loan_available'          => $data['loan_available'] ?? null,
                'description'             => $data['description'] ?? null,
            ]
        );

        // special_typeオプションの更新（一旦削除して再登録）
        $record->options()->where('option_category', 'special_type')->delete();

        $specialMap = [
            'special_one_owner'   => 'one_owner',
            'special_camping_car' => 'camping_car',
            'special_welfare_car' => 'welfare_car',
            'special_unused'      => 'unused',
            'special_eco_car'     => 'eco_car',
        ];

        $maxOrder = $record->options()->max('display_order') ?? 1000;
        foreach ($specialMap as $key => $value) {
            if (!empty($data[$key])) {
                StkCarOptions::create([
                    'car_id'          => $record->id,
                    'option_category' => 'special_type',
                    'option_name'     => $value,
                    'is_equipped'     => 1,
                    'display_order'   => ++$maxOrder,
                ]);
            }
        }

        // 装備仕様オプションの更新（一旦削除して再登録）
        $equipmentCategories = ['safety', 'basic', 'seat', 'dress_up', 'environmental'];
        $record->options()->whereIn('option_category', $equipmentCategories)->delete();

        $equipmentMap = [
            'safety'        => MstEquipmentSafety::where('is_active', 1)->orderBy('sort_order')->get(),
            'basic'         => MstEquipmentBasic::where('is_active', 1)->orderBy('sort_order')->get(),
            'seat'          => MstSeatOption::where('is_active', 1)->orderBy('sort_order')->get(),
            'dress_up'      => MstEquipmentDressup::where('is_active', 1)->orderBy('sort_order')->get(),
            'environmental' => MstEquipmentEnv::where('is_active', 1)->orderBy('sort_order')->get(),
        ];

        $equipOrder = 1;
        foreach ($equipmentMap as $category => $items) {
            foreach ($items as $item) {
                $key = "equipment_{$category}_{$item->value}";
                if (!empty($data[$key])) {
                    StkCarOptions::create([
                        'car_id'          => $record->id,
                        'option_category' => $category,
                        'option_name'     => $item->value,
                        'is_equipped'     => 1,
                        'display_order'   => $equipOrder++,
                    ]);
                }
                unset($data[$key]);
            }
        }

        // その他オプションの更新（一旦削除して再登録）
        $otherCategories = ['audio', 'navigation', 'special_type', 'other'];
        $record->options()->whereIn('option_category', $otherCategories)->delete();

        $maxOrder = $record->options()->max('display_order') ?? 1000;
        foreach ($data['other_options'] ?? [] as $option) {
            if (empty($option['option_name'])) continue;
            StkCarOptions::create([
                'car_id'          => $record->id,
                'option_category' => $option['option_category'],
                'option_name'     => $option['option_name'],
                'is_equipped'     => 1,
                'display_order'   => ++$maxOrder,
            ]);
        }

        unset($data['other_options']);

        // stk_cars側に不要なキーを除去
        unset(
            $data['special_one_owner'],
            $data['special_camping_car'],
            $data['special_welfare_car'],
            $data['special_unused'],
            $data['special_eco_car'],
        );

        // stk_cars側に不要なキーを除去
        unset(
            $data['first_registration_date'],
            $data['inspection_expire_date'],
            $data['inspection_status'],
            $data['drive_system'],
            $data['displacement'],
            $data['steering_wheel'],
            $data['number_of_doors'],
            $data['slide_door'],
            $data['riding_capacity'],
            $data['loan_available'],
            $data['description'],
        );

        return $data;
    }
}