INSERT INTO mst_displacement_lists (name, min_amount, max_amount, is_unlimited ,created_at, updated_at) VALUES
('800cc以下', 800, NULL, 1, NOW(), NOW()),
('1,000〜1,400cc以下', 1000, 1400, 0, NOW(), NOW()),
('1,500〜1,900cc以下', 1500, 1900, 0, NOW(), NOW()),
('2,000〜2,400cc以下', 2000, 2400, 0, NOW(), NOW()),
('2,500〜2,900cc以下', 2500, 2900, 0, NOW(), NOW()),
('3,000〜4,000cc以下', 3000, 4000, 0, NOW(), NOW()),
('4,000cc以上', 4000, NULL, 1, NOW(), NOW());
