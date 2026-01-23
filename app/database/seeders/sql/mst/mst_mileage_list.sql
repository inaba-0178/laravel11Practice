INSERT INTO mst_mileage_lists (name, min_amount, max_amount, is_unlimited ,created_at, updated_at) VALUES
('1万km以上', 10000, NULL, 1, NOW(), NOW()),
('1〜3万km', 10000, 29999, 0, NOW(), NOW()),
('3〜5万km', 30000, 49999, 0, NOW(), NOW()),
('5〜10万km', 50000, 99999,0, NOW(), NOW()),
('10〜15万km', 100000, 149999,0, NOW(), NOW()),
('15万km', 150000, NULL,1, NOW(), NOW());
