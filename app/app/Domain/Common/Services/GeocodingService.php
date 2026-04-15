<?php

declare(strict_types=1);

namespace App\Domain\Common\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Constants\PostalCodeConstants;

final class GeocodingService
{
    /**
     * 郵便番号から住所情報を取得
     */
    public function fetchAddressFromPostalCode(string $postalCode): ?array
    {
        $code = str_replace('-', '', $postalCode);
        if (strlen($code) !== PostalCodeConstants::DIGITS) return null;

        try {
            $response = Http::get(
                "https://zipcloud.ibsnet.co.jp/api/search?zipcode={$code}"
            );

            if (!$response->ok()) return null;

            return $response->json()['results'][0] ?? null;

        } catch (\Exception $e) {
            Log::error('GeocodingService fetchAddressFromPostalCode error:', [
                'postalCode' => $postalCode,
                'message'    => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * 住所から緯度・経度を取得（国土地理院API）
     */
    public function fetchCoordinatesFromAddress(string $address): ?array
    {
        try {
            $response = Http::get(
                "https://msearch.gsi.go.jp/address-search/AddressSearch?q=" . urlencode($address)
            );

            if (!$response->ok()) return null;

            $result = $response->json()[0] ?? null;
            if (!$result) return null;

            [$longitude, $latitude] = $result['geometry']['coordinates'];

            return [
                'latitude'  => $latitude,
                'longitude' => $longitude,
            ];

        } catch (\Exception $e) {
            Log::error('GeocodingService fetchCoordinatesFromAddress error:', [
                'address' => $address,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * 郵便番号から緯度・経度まで一括取得
     */
    public function resolveFromPostalCode(string $postalCode): ?array
    {
        $addressResult = $this->fetchAddressFromPostalCode($postalCode);
        if (!$addressResult) return null;

        $address     = $addressResult['address1'] . $addressResult['address2'] . $addressResult['address3'];
        $coordinates = $this->fetchCoordinatesFromAddress($address);

        return [
            'address1'  => $addressResult['address1'],
            'address2'  => $addressResult['address2'],
            'address3'  => $addressResult['address3'],
            'latitude'  => $coordinates['latitude'] ?? null,
            'longitude' => $coordinates['longitude'] ?? null,
        ];
    }
}