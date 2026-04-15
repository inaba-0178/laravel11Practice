<?php

declare(strict_types=1);

namespace App\Filament\Resources\DealerShopResource\Concerns;

use App\Domain\Common\Services\GeocodingService;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use Filament\Notifications\Notification;

trait ValidatesDealerShopData
{
    private function validateDealerShopData(array $data): void
    {
        // 郵便番号が入力されていない場合はスキップ
        if (empty($data['postal_code'])) return;

        $geocoding = new GeocodingService();
        $result    = $geocoding->resolveFromPostalCode($data['postal_code']);

        if (!$result) return;

        $region = MstRegions::where('name', 'like', '%' . $result['address1'] . '%')->first();
        if (!$region) return;

        // 地方チェック
        if (!empty($data['area_code']) && (int) $data['area_code'] !== (int) $region->area_code) {
            Notification::make()
                ->title('入力内容に誤りがあります')
                ->body('地方の選択した値が、郵便番号で設定された場所と違います。')
                ->danger()
                ->send();

            $this->halt();
        }

        // 都道府県チェック
        if (!empty($data['region_id']) && (int) $data['region_id'] !== (int) $region->id) {
            Notification::make()
                ->title('入力内容に誤りがあります')
                ->body('都道府県の選択した値が、郵便番号で設定された場所と違います。')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}