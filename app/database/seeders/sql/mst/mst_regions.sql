-- mst_regions: 47都道府県マスタデータ
INSERT INTO mst_regions (area_code, name, query_param, sort_order, created_at, updated_at) VALUES
-- 北海道・東北
(1, '北海道', 'hokkaido', 1, NOW(), NOW()),
(2, '青森県', 'aomori', 2, NOW(), NOW()),
(2, '岩手県', 'iwate', 3, NOW(), NOW()),
(2, '宮城県', 'miyagi', 4, NOW(), NOW()),
(2, '秋田県', 'akita', 5, NOW(), NOW()),
(2, '山形県', 'yamagata', 6, NOW(), NOW()),
(2, '福島県', 'fukushima', 7, NOW(), NOW()),

-- 関東
(3, '茨城県', 'ibaraki', 8, NOW(), NOW()),
(3, '栃木県', 'tochigi', 9, NOW(), NOW()),
(3, '群馬県', 'gunma', 10, NOW(), NOW()),
(3, '埼玉県', 'saitama', 11, NOW(), NOW()),
(3, '千葉県', 'chiba', 12, NOW(), NOW()),
(3, '東京都', 'tokyo', 13, NOW(), NOW()),
(3, '神奈川県', 'kanagawa', 14, NOW(), NOW()),

-- 北陸・甲信越
(6, '新潟県', 'niigata', 15, NOW(), NOW()),
(6, '富山県', 'toyama', 16, NOW(), NOW()),
(6, '石川県', 'ishikawa', 17, NOW(), NOW()),
(6, '福井県', 'fukui', 18, NOW(), NOW()),
(6, '山梨県', 'yamanashi', 19, NOW(), NOW()),
(6, '長野県', 'nagano', 20, NOW(), NOW()),

-- 東海
(7, '岐阜県', 'gifu', 21, NOW(), NOW()),
(7, '静岡県', 'shizuoka', 22, NOW(), NOW()),
(7, '愛知県', 'aichi', 23, NOW(), NOW()),
(7, '三重県', 'mie', 24, NOW(), NOW()),

-- 関西
(4, '滋賀県', 'shiga', 25, NOW(), NOW()),
(4, '京都府', 'kyoto', 26, NOW(), NOW()),
(4, '大阪府', 'osaka', 27, NOW(), NOW()),
(4, '兵庫県', 'hyogo', 28, NOW(), NOW()),
(4, '奈良県', 'nara', 29, NOW(), NOW()),
(4, '和歌山県', 'wakayama', 30, NOW(), NOW()),

-- 中国
(8, '鳥取県', 'tottori', 31, NOW(), NOW()),
(8, '島根県', 'shimane', 32, NOW(), NOW()),
(8, '岡山県', 'okayama', 33, NOW(), NOW()),
(8, '広島県', 'hiroshima', 34, NOW(), NOW()),
(8, '山口県', 'yamaguchi', 35, NOW(), NOW()),

-- 四国
(5, '徳島県', 'tokushima', 36, NOW(), NOW()),
(5, '香川県', 'kagawa', 37, NOW(), NOW()),
(5, '愛媛県', 'ehime', 38, NOW(), NOW()),
(5, '高知県', 'kochi', 39, NOW(), NOW()),

-- 九州
(9, '福岡県', 'fukuoka', 40, NOW(), NOW()),
(9, '佐賀県', 'saga', 41, NOW(), NOW()),
(9, '長崎県', 'nagasaki', 42, NOW(), NOW()),
(9, '熊本県', 'kumamoto', 43, NOW(), NOW()),
(9, '大分県', 'oita', 44, NOW(), NOW()),
(9, '宮崎県', 'miyazaki', 45, NOW(), NOW()),
(9, '鹿児島県', 'kagoshima', 46, NOW(), NOW()),

-- 沖縄
(10, '沖縄県', 'okinawa', 47, NOW(), NOW());
