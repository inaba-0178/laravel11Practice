-- mst_regions: 47都道府県マスタデータ
INSERT INTO mst_regions (name, query_param, sort_order, created_at, updated_at) VALUES
-- 北海道・東北
('北海道', 'hokkaido', 1, NOW(), NOW()),
('青森県', 'aomori', 2, NOW(), NOW()),
('岩手県', 'iwate', 3, NOW(), NOW()),
('宮城県', 'miyagi', 4, NOW(), NOW()),
('秋田県', 'akita', 5, NOW(), NOW()),
('山形県', 'yamagata', 6, NOW(), NOW()),
('福島県', 'fukushima', 7, NOW(), NOW()),

-- 関東
('茨城県', 'ibaraki', 8, NOW(), NOW()),
('栃木県', 'tochigi', 9, NOW(), NOW()),
('群馬県', 'gunma', 10, NOW(), NOW()),
('埼玉県', 'saitama', 11, NOW(), NOW()),
('千葉県', 'chiba', 12, NOW(), NOW()),
('東京都', 'tokyo', 13, NOW(), NOW()),
('神奈川県', 'kanagawa', 14, NOW(), NOW()),

-- 中部
('新潟県', 'niigata', 15, NOW(), NOW()),
('富山県', 'toyama', 16, NOW(), NOW()),
('石川県', 'ishikawa', 17, NOW(), NOW()),
('福井県', 'fukui', 18, NOW(), NOW()),
('山梨県', 'yamanashi', 19, NOW(), NOW()),
('長野県', 'nagano', 20, NOW(), NOW()),
('岐阜県', 'gifu', 21, NOW(), NOW()),
('静岡県', 'shizuoka', 22, NOW(), NOW()),
('愛知県', 'aichi', 23, NOW(), NOW()),
('三重県', 'mie', 24, NOW(), NOW()),

-- 近畿
('滋賀県', 'shiga', 25, NOW(), NOW()),
('京都府', 'kyoto', 26, NOW(), NOW()),
('大阪府', 'osaka', 27, NOW(), NOW()),
('兵庫県', 'hyogo', 28, NOW(), NOW()),
('奈良県', 'nara', 29, NOW(), NOW()),
('和歌山県', 'wakayama', 30, NOW(), NOW()),

-- 中国・四国
('鳥取県', 'tottori', 31, NOW(), NOW()),
('島根県', 'shimane', 32, NOW(), NOW()),
('岡山県', 'okayama', 33, NOW(), NOW()),
('広島県', 'hiroshima', 34, NOW(), NOW()),
('山口県', 'yamaguchi', 35, NOW(), NOW()),
('徳島県', 'tokushima', 36, NOW(), NOW()),
('香川県', 'kagawa', 37, NOW(), NOW()),
('愛媛県', 'ehime', 38, NOW(), NOW()),
('高知県', 'kochi', 39, NOW(), NOW()),

-- 九州・沖縄
('福岡県', 'fukuoka', 40, NOW(), NOW()),
('佐賀県', 'saga', 41, NOW(), NOW()),
('長崎県', 'nagasaki', 42, NOW(), NOW()),
('熊本県', 'kumamoto', 43, NOW(), NOW()),
('大分県', 'oita', 44, NOW(), NOW()),
('宮崎県', 'miyazaki', 45, NOW(), NOW()),
('鹿児島県', 'kagoshima', 46, NOW(), NOW()),
('沖縄県', 'okinawa', 47, NOW(), NOW());
