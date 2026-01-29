INSERT INTO mst_featured_brands (manufacturer_code, position, sort_order, is_active, created_at, updated_at) VALUES
-- 上段：国産車9社
('LEXUS01', 'jp-top-row', 100, 1, NOW(), NOW()),
('TOYOTA01', 'jp-top-row', 110, 1, NOW(), NOW()),
('HONDA01', 'jp-top-row', 120, 1, NOW(), NOW()),
('NISSAN01', 'jp-top-row', 130, 1, NOW(), NOW()),
('SUZUKI01', 'jp-top-row', 140, 1, NOW(), NOW()),
('DAIHATSU01', 'jp-top-row', 150, 1, NOW(), NOW()),
('MAZDA01', 'jp-top-row', 160, 1, NOW(), NOW()),
('SUBARU01', 'jp-top-row', 170, 1, NOW(), NOW()),
('MITSUBISHI01', 'jp-top-row', 180, 1, NOW(), NOW()),

-- 下段：輸入車9社
('MBENZ01', 'import-top-row', 200, 1, NOW(), NOW()),
('BMW01', 'import-top-row', 210, 1, NOW(), NOW()),
('VW01', 'import-top-row', 220, 1, NOW(), NOW()),
('AUDI01', 'import-top-row', 230, 1, NOW(), NOW()),
('MINI01', 'import-top-row', 240, 1, NOW(), NOW()),
('PORSCHE01', 'import-top-row', 250, 1, NOW(), NOW()),
('VOLVO01', 'import-top-row', 260, 1, NOW(), NOW()),
('PEUGEOT01', 'import-top-row', 270, 1, NOW(), NOW()),
('LANDROVER01', 'import-top-row', 280, 1, NOW(), NOW());
