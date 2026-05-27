<?php

declare(strict_types=1);

namespace tests\Unit\Domain\Mst\DataTransformers;

use App\Domain\Mst\DataTransformers\MstRowTransformer;
use Tests\TestCase;

class MstRowTransformerTest extends TestCase
{
    // ==========================================
    // mapHeaders
    // ==========================================

    /** @test */
    public function mapHeaders_日本語ヘッダーを英語カラム名に変換する(): void
    {
        $rows = [
            ['ID' => '1', 'シリーズ名' => 'CT', 'メーカー名' => 'LEXUS'],
        ];

        $result = MstRowTransformer::mapHeaders($rows, 'mst_car_series_body_types');

        $this->assertArrayHasKey('series_id', $result[0]);
        $this->assertArrayHasKey('manufacturer_name', $result[0]);
        $this->assertArrayNotHasKey('シリーズ名', $result[0]);
        $this->assertArrayNotHasKey('メーカー名', $result[0]);
    }

    /** @test */
    public function mapHeaders_空配列の場合は空配列を返す(): void
    {
        $result = MstRowTransformer::mapHeaders([], 'mst_car_series_body_types');
        $this->assertSame([], $result);
    }

    // ==========================================
    // normalizeBoolean
    // ==========================================

    /** @test */
    public function normalizeBoolean_TRUE文字列を1に変換する(): void
    {
        $rows   = [['is_active' => 'TRUE', 'is_main' => 'true']];
        $result = MstRowTransformer::normalizeBoolean($rows);

        $this->assertSame(1, $result[0]['is_active']);
        $this->assertSame(1, $result[0]['is_main']);
    }

    /** @test */
    public function normalizeBoolean_FALSE文字列を0に変換する(): void
    {
        $rows   = [['is_active' => 'FALSE', 'is_main' => 'false']];
        $result = MstRowTransformer::normalizeBoolean($rows);

        $this->assertSame(0, $result[0]['is_active']);
        $this->assertSame(0, $result[0]['is_main']);
    }

    /** @test */
    public function normalizeBoolean_その他の値はそのまま(): void
    {
        $rows   = [['name' => 'CT', 'sort_order' => '1']];
        $result = MstRowTransformer::normalizeBoolean($rows);

        $this->assertSame('CT', $result[0]['name']);
        $this->assertSame('1', $result[0]['sort_order']);
    }

    // ==========================================
    // normalizeNumbers
    // ==========================================

    /** @test */
    public function normalizeNumbers_カンマ区切り数値を正規化する(): void
    {
        $rows   = [['price' => '1,000,000']];
        $result = MstRowTransformer::normalizeNumbers($rows);

        $this->assertSame('1000000', $result[0]['price']);
    }

    /** @test */
    public function normalizeNumbers_null文字列をnullに変換する(): void
    {
        $rows   = [['description' => 'null', 'url' => 'NULL']];
        $result = MstRowTransformer::normalizeNumbers($rows);

        $this->assertNull($result[0]['description']);
        $this->assertNull($result[0]['url']);
    }

    /** @test */
    public function normalizeNumbers_非公表をnullに変換する(): void
    {
        $rows   = [['value' => '非公表', 'other' => '-', 'na' => 'N/A']];
        $result = MstRowTransformer::normalizeNumbers($rows);

        $this->assertNull($result[0]['value']);
        $this->assertNull($result[0]['other']);
        $this->assertNull($result[0]['na']);
    }

    // ==========================================
    // resolveManufacturerIds
    // ==========================================

    /** @test */
    public function resolveManufacturerIds_メーカー名をIDに変換する(): void
    {
        $rows          = [['manufacturer_id' => 'LEXUS', 'name' => 'CT']];
        $manufacturers = ['LEXUS' => 1, 'TOYOTA' => 2];

        $result = MstRowTransformer::resolveManufacturerIds($rows, $manufacturers);

        $this->assertSame(1, $result[0]['manufacturer_id']);
    }

    /** @test */
    public function resolveManufacturerIds_既に数値の場合はそのまま(): void
    {
        $rows          = [['manufacturer_id' => 1, 'name' => 'CT']];
        $manufacturers = ['LEXUS' => 1, 'TOYOTA' => 2];

        $result = MstRowTransformer::resolveManufacturerIds($rows, $manufacturers);

        $this->assertSame(1, $result[0]['manufacturer_id']);
    }

    /** @test */
    public function resolveManufacturerIds_空配列の場合はそのまま(): void
    {
        $rows   = [['manufacturer_id' => 'LEXUS']];
        $result = MstRowTransformer::resolveManufacturerIds($rows, []);

        $this->assertSame('LEXUS', $result[0]['manufacturer_id']);
    }

    /** @test */
    public function resolveManufacturerIds_manufacturer_idカラムがない場合はそのまま(): void
    {
        $rows          = [['name' => 'CT']];
        $manufacturers = ['LEXUS' => 1];

        $result = MstRowTransformer::resolveManufacturerIds($rows, $manufacturers);

        $this->assertArrayNotHasKey('manufacturer_id', $result[0]);
    }

    // ==========================================
    // resolveBodyTypeIds
    // ==========================================

    /** @test */
    public function resolveBodyTypeIds_ボディタイプ名をIDに変換する_body_type(): void
    {
        $rows      = [['body_type' => 'セダン']];
        $bodyTypes = ['セダン' => 6, 'SUV・クロカン' => 5];

        $result = MstRowTransformer::resolveBodyTypeIds($rows, $bodyTypes);

        $this->assertSame(6, $result[0]['body_type']);
    }

    /** @test */
    public function resolveBodyTypeIds_ボディタイプ名をIDに変換する_body_type_id(): void
    {
        $rows      = [['body_type_id' => 'ミニバン']];
        $bodyTypes = ['ミニバン' => 3];

        $result = MstRowTransformer::resolveBodyTypeIds($rows, $bodyTypes);

        $this->assertSame(3, $result[0]['body_type_id']);
    }

    /** @test */
    public function resolveBodyTypeIds_空配列の場合はそのまま(): void
    {
        $rows   = [['body_type' => 'セダン']];
        $result = MstRowTransformer::resolveBodyTypeIds($rows, []);

        $this->assertSame('セダン', $result[0]['body_type']);
    }

    // ==========================================
    // resolveSeriesIds
    // ==========================================

    /** @test */
    public function resolveSeriesIds_メーカー名とシリーズ名でIDに変換する(): void
    {
        $rows          = [['series_id' => 'CT', 'manufacturer_name' => 'LEXUS', 'file_path' => '']];
        $manufacturers = ['LEXUS' => 1, 'TOYOTA' => 2];
        $series        = ['1_CT' => 1, '2_カローラ' => 72];

        $result = MstRowTransformer::resolveSeriesIds($rows, $manufacturers, $series, 'mst_car_series_body_types');

        $this->assertSame(1, $result[0]['series_id']);
        $this->assertArrayNotHasKey('manufacturer_name', $result[0]);
    }

    /** @test */
    public function resolveSeriesIds_file_pathからメーカー名を取得してIDに変換する(): void
    {
        $rows          = [['series_id' => 'CT', 'file_path' => 'LEXUS/ct.png']];
        $manufacturers = ['LEXUS' => 1];
        $series        = ['1_CT' => 1];

        $result = MstRowTransformer::resolveSeriesIds($rows, $manufacturers, $series, 'mst_car_series_images');

        $this->assertSame(1, $result[0]['series_id']);
    }

    /** @test */
    public function resolveSeriesIds_メーカー名もfile_pathもない場合はシリーズ名のみで検索(): void
    {
        $rows          = [['series_id' => 'プリウス']];
        $manufacturers = ['TOYOTA' => 2];
        $series        = ['2_プリウス' => 178];

        $result = MstRowTransformer::resolveSeriesIds($rows, $manufacturers, $series, 'mst_car_series_images');

        $this->assertSame(178, $result[0]['series_id']);
    }

    /** @test */
    public function resolveSeriesIds_空配列の場合はそのまま(): void
    {
        $rows   = [['series_id' => 'CT']];
        $result = MstRowTransformer::resolveSeriesIds($rows, [], [], 'mst_car_series_body_types');

        $this->assertSame('CT', $result[0]['series_id']);
    }

    /** @test */
    public function resolveSeriesIds_数字のみのシリーズ名も変換する(): void
    {
        $rows          = [['series_id' => '86', 'manufacturer_name' => 'TOYOTA']];
        $manufacturers = ['TOYOTA' => 2];
        $series        = ['2_86' => 28];

        $result = MstRowTransformer::resolveSeriesIds($rows, $manufacturers, $series, 'mst_car_series_body_types');

        $this->assertSame(28, $result[0]['series_id']);
        $this->assertArrayNotHasKey('manufacturer_name', $result[0]);
    }

    // ==========================================
    // resolveVehicleIds
    // ==========================================

    /** @test */
    public function resolveVehicleIds_車両名をIDに変換する(): void
    {
        $rows     = [['vehicle_id' => 'CT 初期型(2011-2014)']];
        $vehicles = ['CT 初期型(2011-2014)' => 1];

        $result = MstRowTransformer::resolveVehicleIds($rows, $vehicles);

        $this->assertSame(1, $result[0]['vehicle_id']);
    }

    /** @test */
    public function resolveVehicleIds_空配列の場合はそのまま(): void
    {
        $rows   = [['vehicle_id' => 'CT 初期型(2011-2014)']];
        $result = MstRowTransformer::resolveVehicleIds($rows, []);

        $this->assertSame('CT 初期型(2011-2014)', $result[0]['vehicle_id']);
    }

    /** @test */
    public function resolveVehicleIds_vehicle_idカラムがない場合はそのまま(): void
    {
        $rows     = [['name' => 'CT 初期型(2011-2014)']];
        $vehicles = ['CT 初期型(2011-2014)' => 1];

        $result = MstRowTransformer::resolveVehicleIds($rows, $vehicles);

        $this->assertArrayNotHasKey('vehicle_id', $result[0]);
    }
}