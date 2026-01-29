-- 一旦available_countries　JPのみにしておく 後で追加する
INSERT INTO mst_body_types (name, name_kana, code, description, available_countries, sort_order, is_active, created_at, updated_at, deleted_at) VALUES
('軽自動車', '軽自動車', 'KeiCars', NULL, '["JP"]', 100, 1, NOW(), NOW(), NULL),
('コンパクトカー', 'コンパクトカー', 'CompactCars', NULL, '["JP"]', 110, 1, NOW(), NOW(), NULL),
('ミニバン', 'ミニバン', 'Minivans', NULL, '["JP"]', 120, 1, NOW(), NOW(), NULL),
('ステーションワゴン', 'ステーションワゴン', 'StationWagons', NULL, '["JP"]', 130, 1, NOW(), NOW(), NULL),
('SUV・クロカン', 'SUV・クロカン', 'SUVs&CrossCountryVehicles', NULL, '["JP"]', 140, 1, NOW(), NOW(), NULL),
('セダン', 'セダン', 'Sedans', NULL, '["JP"]', 150, 1, NOW(), NOW(), NULL),
('キャンピングカー', 'キャンピングカー', 'Campers', NULL, '["JP"]', 160, 1, NOW(), NOW(), NULL),
('クーペ', 'クーペ', 'Coupes', NULL, '["JP"]', 170, 1, NOW(), NOW(), NULL),
('ハイブリッド', 'ハイブリッド', 'Hybrid', NULL, '["JP"]', 200, 1, NOW(), NOW(), NULL),
('ハッチバッグ', 'ハッチバッグ', 'Hatchback', NULL, '["JP"]', 210, 1, NOW(), NOW(), NULL),
('オープンカー', 'オープンカー', 'Convertibles', NULL, '["JP"]', 220, 1, NOW(), NOW(), NULL),
('ピックアップトラック', 'ピックアップトラック', 'PickupTrucks', NULL, '["JP"]', 230, 1, NOW(), NOW(), NULL),
('福祉車両', '福祉車両', 'WelfareVehicles', NULL, '["JP"]', 240, 1, NOW(), NOW(), NULL),
('商用車・バン', '商用車・バン', 'CommercialVehicles&Vans', NULL, '["JP"]', 250, 1, NOW(), NOW(), NULL),
('トラック', 'トラック', 'Trucks', NULL, '["JP"]', 260, 1, NOW(), NOW(), NULL),
('その他', 'その他', 'Other', NULL, '["JP"]', 999, 1, NOW(), NOW(), NULL);
