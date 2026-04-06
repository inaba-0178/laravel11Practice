<?php

declare(strict_types=1);

namespace App\Filament\Resources\CarRegistrationResource\Pages;

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
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use App\Constants\SpecialTypeOption;
use App\Constants\CarStatus;
use App\Infrastructure\Eloquent\User\StkCar;

class EditCarRegistration extends EditRecord
{
    protected static string $resource = CarRegistrationResource::class;
    protected static string $view = 'filament.resources.car-registration-resource.pages.edit-car-registration';
    // デフォルトのフッターボタンを非表示
    protected function getFormActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormContentFooter(): ?\Illuminate\Contracts\View\View
    {
        return null;
    }

    public function getFormHeader(): ?\Illuminate\Contracts\View\View  
    {
        $record = $this->getRecord();
        
        if ($record->status === 'rejected' && $record->rejection_reason) {
            return view('filament.modals.rejection-reason', [
                'record' => $record,
            ]);
        }
        
        return null;
    }

    protected function getHeaderActions(): array
    {
        $record    = $this->getRecord();
        $hasImages = StkCarImages::where('car_id', $record->id)->exists();
        $actions   = [];

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
                    $this->saveAndRequestApproval($record, clearRejection: true);
                });
        }

        // ===== 再承認依頼ボタン =====
        if ($record->status === 'rejected') {
            $actions[] = Action::make('request_approval')
                ->label('再承認依頼')
                ->color('warning')
                ->tooltip(!$hasImages ? '画像を1枚以上アップロードしてください' : null)
                ->requiresConfirmation()
                ->modalHeading('再承認依頼を管理者に送ります')
                ->modalDescription('入力内容や画像アップロードに問題ありませんか？')
                ->modalSubmitActionLabel('再承認依頼')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () use ($record) {
                    $this->saveAndRequestApproval($record, clearRejection: true);
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
                $this->dispatch('$refresh');
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

    // ===== ディーラー返答保存 =====
    public function saveDealerResponse(array $data): void
    {
        $record = $this->getRecord();
    
        if (!$record->rejection_reason || !is_array($record->rejection_reason)) {
            return;
        }
    
        $reason = $record->rejection_reason;
    
        // 全体返答を保存
        $reason['general_response'] = $data['general_response'] ?? '';
    
        // 画像ごとの返答を保存
        $imageResponses = $data['image_responses'] ?? [];
        $reason['flagged_images'] = collect($reason['flagged_images'] ?? [])
            ->map(function ($img) use ($imageResponses) {
                $id = $img['id'];
                if (isset($imageResponses[$id])) {
                    $img['dealer_response'] = $imageResponses[$id];
                }
                return $img;
            })
            ->toArray();
    
        // 項目ごとの返答を保存
        $itemResponses = $data['item_responses'] ?? [];
        $reason['items'] = collect($reason['items'] ?? [])
            ->map(function ($item, $idx) use ($itemResponses) {
                if (isset($itemResponses[$idx])) {
                    $item['dealer_response'] = $itemResponses[$idx];
                }
                return $item;
            })
            ->toArray();
    
        $record->update(['rejection_reason' => $reason]);
    
        Notification::make()
            ->title('対応内容を保存しました')
            ->success()
            ->send();
    }

    // EditCarRegistration クラス内に以下を追加
    protected function getHeaderWidgets(): array
    {
        return [];
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
    
        // ローンが設定されている場合はセクションを開く
        $data['has_loans'] = $this->getRecord()->loans()->exists();

        return $data;
    }

    /**
     * 承認依頼時に保存する処理
     * @param StkCar    $record
     * @param bool      $clearRejection
     * 
     * @return void
     */
    private function saveAndRequestApproval(StkCar $record, bool $clearRejection = true): void
    {
        // 自動保存
        $this->save();

        DB::transaction(function () use ($record, $clearRejection) {
            $updateData = ['status' => 'pending'];
            if ($clearRejection) {
                $updateData['rejection_reason'] = null;
            }
            $record->update($updateData);
        });

        $seriesName = $record->series?->series_name ?? '不明';
        $dealerName = $record->dealer?->name ?? '不明';

        User::whereIn('role', ['super', 'admin'])
            ->where('is_active', 1)
            ->get()
            ->each(fn (User $u) => $u->notify(
                new CarRegistrationPendingNotification($seriesName, $dealerName, $record->id)
            ));

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
    }

    protected function afterSave(): void
    {
        $formData = $this->form->getRawState();
        $loans    = $formData['loans'] ?? [];

        foreach ($loans as $loanData) {
            $planId = $loanData['dealer_loan_plan_id'] ?? null;
            $loanId = $loanData['id'] ?? null;

            if (!$loanId) continue;

            $loan = \App\Infrastructure\Eloquent\User\StkCarLoan::find($loanId);
            if (!$loan)
            {
                continue;
            }

            if (str_starts_with((string) $planId, 'mst_')) {
                $mstId       = str_replace('mst_', '', $planId);
                $defaultPlan = \App\Infrastructure\Eloquent\Mst\MstLoanPlan::find($mstId);
                if ($defaultPlan) {
                    $loan->update([
                        'dealer_loan_plan_id'     => null,
                        'snapshot_plan_name'      => 'システムデフォルト',
                        'snapshot_rate'           => $defaultPlan->interest_rate,
                        'snapshot_months_options' => $defaultPlan->months_options,
                        'snapshot_min_months'     => $defaultPlan->min_months,
                        'snapshot_max_months'     => $defaultPlan->max_months,
                        'snapshot_bonus_amount'   => null,
                        'snapshot_bonus_times'    => null,
                    ]);
                }
            }
            else
            {
                $plan = \App\Infrastructure\Eloquent\User\StkDealerLoanPlan::find($planId);
                if ($plan) {
                    $loan->update([
                        'dealer_loan_plan_id'     => $plan->id,
                        'snapshot_plan_name'      => $plan->name,
                        'snapshot_rate'           => $plan->interest_rate,
                        'snapshot_months_options' => $plan->months_options,
                        'snapshot_min_months'     => $plan->min_months,
                        'snapshot_max_months'     => $plan->max_months,
                        'snapshot_bonus_amount'   => $plan->bonus_amount,
                        'snapshot_bonus_times'    => $plan->bonus_times,
                    ]);
                }
                continue;
            }
        }
    }
}