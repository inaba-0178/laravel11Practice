<?php

declare(strict_types=1);

namespace App\Filament\Resources\CarRegistrationResource\Pages;

use App\Filament\Resources\CarRegistrationResource;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkCarDetails;
use App\Infrastructure\Eloquent\User\StkCarOptions;
use App\Infrastructure\Eloquent\Mst\MstEquipmentSafety;
use App\Infrastructure\Eloquent\Mst\MstEquipmentBasic;
use App\Infrastructure\Eloquent\Mst\MstEquipmentDressup;
use App\Infrastructure\Eloquent\Mst\MstEquipmentEnv;
use App\Infrastructure\Eloquent\Mst\MstSeatOption;
use App\Notifications\CarRegistrationPendingNotification;
use App\Constants\CarStatus;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Filament\Resources\CarRegistrationResource\Concerns\ValidatesCarData;
use App\Constants\AudioOption;
use App\Constants\NaviOption;

class CreateCarRegistration extends CreateRecord
{
    use ValidatesCarData;
    protected static string $resource = CarRegistrationResource::class;

    // 保存時のステータスを保持するプロパティ
    protected string $saveStatus = CarStatus::DRAFT;

    // デフォルトのフッターボタンを非表示
    protected function getFormActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getHeaderActions(): array
    {
        return [

            // ===== 承認依頼ボタン =====
            Action::make('request_approval')
                ->label('承認依頼')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('承認依頼を管理者に送ります')
                ->modalDescription('入力内容や画像アップロードに問題ありませんか？')
                ->modalSubmitActionLabel('承認依頼')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () {
                    // pendingステータスで保存
                    $this->saveStatus = CarStatus::PENDING;
                    $this->create();

                    $record     = $this->record;
                    $seriesName = $record->series?->series_name ?? '不明';
                    $dealerName = $record->dealer?->name ?? '不明';

                    User::whereIn('role', ['super', 'admin'])
                        ->where('is_active', 1)
                        ->get()
                        ->each(fn (User $u) => $u->notify(
                            new CarRegistrationPendingNotification($seriesName, $dealerName, $record->id)
                        ));

                    Notification::make()
                        ->title('承認依頼を管理者に送りました。承認されるまでには時間がかかりますのでお待ちください。')
                        ->success()
                        ->send();
                }),

            // ===== 画像アップロードボタン（作成前は案内のみ） =====
            Action::make('upload_images')
                ->label('画像アップロード')
                ->color('info')
                ->action(function () {
                    Notification::make()
                        ->title('先に「保存」してから画像をアップロードしてください')
                        ->warning()
                        ->send();
                }),

            // ===== 保存ボタン =====
            Action::make('save')
                ->label('保存')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('一時保存しますか？')
                ->modalDescription('入力内容を下書きとして保存します。')
                ->modalSubmitActionLabel('保存する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () {
                    // draftステータスで保存
                    $this->saveStatus = CarStatus::DRAFT;
                    $this->create();

                    Notification::make()
                        ->title('一時保存しました')
                        ->body('引き続き編集するか、一覧に戻ることができます。')
                        ->success()
                        ->send();
                }),

            // ===== キャンセルボタン =====
            Action::make('cancel')
                ->label('キャンセル')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $this->validateCarData($data);

        $user = Auth::user();

        return DB::transaction(function () use ($data, $user) {

            // ===== stk_cars 登録 =====
            $car = StkCar::create([
                'dealer_id'             => $user->dealer_id,
                'manufacturer_id'       => $data['manufacturer_id'],
                'series_id'             => $data['series_id'],
                'vehicle_id'            => $data['vehicle_id'],
                'year_version_id'       => $data['year_version_id'] ?? null,
                'stock_number'          => null,
                'status'                => $this->saveStatus, // draft or pending
                'price'                 => $data['price'],
                'price_display_type'    => $data['price_display_type'] ?? 'actual',
                'model_year'            => $data['model_year'] ?? null,
                'mileage'               => $data['mileage'],
                'body_type_id'          => $data['body_type_id'] ?? null,
                'color'                 => $data['color'],
                'color_group'           => $data['color_group'] ?? null,
                'transmission'          => $data['transmission'] ?? null,
                'fuel_type'             => $data['fuel_type'] ?? null,
                'region_id'             => $data['region_id'],
                'repair_history'        => $data['repair_history'] ?? 'unknown',
                'published_at'          => null,
                'recycle_fee'           => $data['recycle_fee'] ?? null,
                'dealer_fee_id'         => $data['dealer_fee_id'] ?? null,
            ]);

            // ===== stk_car_details 登録 =====
            StkCarDetails::create([
                'car_id'                  => $car->id,
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
            ]);

            // ===== stk_car_options 登録（装備仕様） =====
            $equipmentMap = [
                'safety'        => MstEquipmentSafety::where('is_active', 1)->orderBy('sort_order')->get(),
                'basic'         => MstEquipmentBasic::where('is_active', 1)->orderBy('sort_order')->get(),
                'seat'          => MstSeatOption::where('is_active', 1)->orderBy('sort_order')->get(),
                'dress_up'      => MstEquipmentDressup::where('is_active', 1)->orderBy('sort_order')->get(),
                'environmental' => MstEquipmentEnv::where('is_active', 1)->orderBy('sort_order')->get(),
            ];

            $displayOrder = 1;
            foreach ($equipmentMap as $category => $items) {
                foreach ($items as $item) {
                    $key = "equipment_{$category}_{$item->value}";
                    if (!empty($data[$key])) {
                        StkCarOptions::create([
                            'car_id'          => $car->id,
                            'option_category' => $category,
                            'option_name'     => $item->value,
                            'is_equipped'     => 1,
                            'display_order'   => $displayOrder++,
                        ]);
                    }
                }
            }

            // ===== stk_car_options 登録（その他・special_type・販売サービス） =====
            $maxOrder = StkCarOptions::where('car_id', $car->id)->max('display_order') ?? 1000;

            $optionsToCreate = [];

            // その他オプション
            foreach ($data['other_options'] ?? [] as $option) {
                if (empty($option['option_name'])) continue;
                $optionsToCreate[] = [
                    'option_category' => $option['option_category'],
                    'option_name'     => $option['option_name'],
                ];
            }

            // special_type
            $specialMap = [
                'special_one_owner'    => 'one_owner',
                'special_camping_car'  => 'camping_car',
                'special_welfare_car'  => 'welfare_car',
                'special_unused'       => 'unused',
                'special_eco_car'      => 'eco_car',
                'special_unregistered' => 'unregistered',
            ];
            foreach ($specialMap as $key => $value) {
                if (!empty($data[$key])) {
                    $optionsToCreate[] = [
                        'option_category' => 'special_type',
                        'option_name'     => $value,
                    ];
                }
            }

            // 販売・サービス情報
            $optionMap = [
                'opt_quality_cert'   => 'quality_cert',
                'opt_purchase_plan'  => 'purchase_plan',
                'opt_sensor_after'   => 'sensor_after',
                'opt_online_consult' => 'online_consult',
            ];
            foreach ($optionMap as $key => $value) {
                if (!empty($data[$key])) {
                    $optionsToCreate[] = [
                        'option_category' => 'other',
                        'option_name'     => $value,
                    ];
                }
            }

            // オーディオ
            $audioMap = [
                'audio_cd'        => AudioOption::CD,
                'audio_dvd'       => AudioOption::DVD,
                'audio_bluetooth' => AudioOption::BLUETOOTH,
                'audio_usb'       => AudioOption::USB,
            ];
            foreach ($audioMap as $key => $value) {
                if (!empty($data[$key])) {
                    $optionsToCreate[] = [
                        'option_category' => 'audio',
                        'option_name'     => $value,
                    ];
                }
            }

            // オーディオメーカー
            if (!empty($data['audio_maker'])) {
                $optionsToCreate[] = [
                    'option_category' => 'audio',
                    'option_name'     => 'maker_' . $data['audio_maker'],
                ];
            }

            // ナビ
            $naviMap = [
                'navi_navi' => NaviOption::NAVI,
                'navi_tv'   => NaviOption::TV,
                'navi_dvd'  => NaviOption::DVD_NAVI,
            ];
            foreach ($naviMap as $key => $value) {
                if (!empty($data[$key])) {
                    $optionsToCreate[] = [
                        'option_category' => 'navigation',
                        'option_name'     => $value,
                    ];
                }
            }

            // 一括登録
            foreach ($optionsToCreate as $option) {
                StkCarOptions::create([
                    'car_id'          => $car->id,
                    'option_category' => $option['option_category'],
                    'option_name'     => $option['option_name'],
                    'is_equipped'     => 1,
                    'display_order'   => ++$maxOrder,
                ]);
            }



            return $car;
        });
    }
    
    protected function afterCreate(): void
    {
        $record   = $this->getRecord();
        $formData = $this->form->getRawState();
        $loans    = $formData['loans'] ?? [];

        // 作成されたloansを順番で取得
        $createdLoans = $record->loans()->orderBy('id')->get();

        foreach ($createdLoans as $index => $loan) {
            $loanData = array_values($loans)[$index] ?? null;
            if (!$loanData) continue;

            $planId = $loanData['dealer_loan_plan_id'] ?? null;

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
            } else {
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
            }
        }
    }
}