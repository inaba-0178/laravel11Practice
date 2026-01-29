INSERT INTO mst_manufacturers (name, name_kana, display_name, code, url, description, country_code, sort_order, is_active, created_at, updated_at, deleted_at) VALUES
('LEXUS', 'レクサス', 'レクサス', 'LEXUS01', NULL, NULL, 'JP', 100, 1, NOW(), NOW(), NULL),
('TOYOTA', 'トヨタ', 'トヨタ', 'TOYOTA01', NULL, NULL, 'JP', 110, 1, NOW(), NOW(), NULL),
('HONDA', 'ホンダ', 'ホンダ', 'HONDA01', NULL, NULL, 'JP', 120, 1, NOW(), NOW(), NULL),
('NISSAN', '日産', 'ニッサン', 'NISSAN01', NULL, NULL, 'JP', 130, 1, NOW(), NOW(), NULL),
('SUZUKI', 'スズキ', 'スズキ', 'SUZUKI01', NULL, NULL, 'JP', 140, 1, NOW(), NOW(), NULL),
('DAIHATSU', 'ダイハツ', 'ダイハツ', 'DAIHATSU01', NULL, NULL, 'JP', 150, 1, NOW(), NOW(), NULL),
('MAZDA', 'マツダ', 'マツダ', 'MAZDA01', NULL, NULL, 'JP', 160, 1, NOW(), NOW(), NULL),
('SUBARU', 'スバル', 'スバル', 'SUBARU01', NULL, NULL, 'JP', 170, 1, NOW(), NOW(), NULL),
('MITSUBISHI', 'ミツビシ', '三菱', 'MITSUBISHI01', NULL, NULL, 'JP', 180, 1, NOW(), NOW(), NULL),
('ISUZU', 'イスヾ', 'いすゞ', 'ISUZU01', NULL, NULL, 'JP', 190, 1, NOW(), NOW(), NULL),
('MITSUOKA', 'ミツオカ', '光岡自動車', 'MITSUOKA01', NULL, NULL, 'JP', 200, 1, NOW(), NOW(), NULL),
('TOMMYKAIRA', 'トミーカイラ', 'トミーカイラ', 'TOMMYKAIRA01', NULL, NULL, 'JP', 210, 1, NOW(), NOW(), NULL),
('HINO', 'ヒノ', '日野自動車', 'HINO01', NULL, NULL, 'JP', 220, 1, NOW(), NOW(), NULL),
('UDTrucks', 'ユーディートラックス', 'UDトラックス', 'UDTRUCKS01', NULL, NULL, 'JP', 230, 1, NOW(), NOW(), NULL),
('MitsubishiFuso', 'ミツビシフソウ', '三菱ふそう', 'MITSUBISHIFUSO01', NULL, NULL, 'JP', 240, 1, NOW(), NOW(), NULL),
('etc', 'その他', '国産車その他', 'ETC01', NULL, NULL, 'JP', 999, 1, NOW(), NOW(), NULL),

--  ドイツ車（DE） 1000番台
('Mercedes-Benz', 'メルセデス・ベンツ', 'メルセデス・ベンツ', 'MBENZ01', NULL, NULL, 'DE', 1000, 1, NOW(), NOW(), NULL),
('Mercedes-AMG', 'メルセデスアムジー', 'メルセデスAMG', 'MBAMG01', NULL, NULL, 'DE', 1010, 1, NOW(), NOW(), NULL),
('Mercedes-Maybach', 'メルセデス・マイバッハ', 'マイバッハ', 'MBMAYBACH01', NULL, NULL, 'DE', 1020, 1, NOW(), NOW(), NULL),
('AMG', 'アムジー', 'AMG', 'AMG01', NULL, NULL, 'DE', 1030, 1, NOW(), NOW(), NULL),
('Maybach', 'マイバッハ', 'マイバッハ', 'MAYBACH01', NULL, NULL, 'DE', 1040, 1, NOW(), NOW(), NULL),
('Smart', 'スマート', 'スマート', 'SMART01', NULL, NULL, 'DE', 1050, 1, NOW(), NOW(), NULL),
('BMW', 'ビーエムダブリュー', 'BMW', 'BMW01', NULL, NULL, 'DE', 1060, 1, NOW(), NOW(), NULL),
('BMWAlpina', 'ビーエムダブリューアルピナ', 'BMWアルピナ', 'BMWALPINA01', NULL, NULL, 'DE', 1070, 1, NOW(), NOW(), NULL),
('Audi', 'アウディ', 'アウディ', 'AUDI01', NULL, NULL, 'DE', 1080, 1, NOW(), NOW(), NULL),
('Volkswagen', 'フォルクスワーゲン', 'VW', 'VW01', NULL, NULL, 'DE', 1090, 1, NOW(), NOW(), NULL),
('Opel', 'オペル', 'オペル', 'OPEL01', NULL, NULL, 'DE', 1100, 1, NOW(), NOW(), NULL),
('Porsche', 'ポルシェ', 'ポルシェ', 'PORSCHE01', NULL, NULL, 'DE', 1110, 1, NOW(), NOW(), NULL),
('Brabus', 'ブラバス', 'ブラバス', 'BRABUS01', NULL, NULL, 'DE', 1120, 1, NOW(), NOW(), NULL),
('Yes', 'イエス', 'イエス', 'YES01', NULL, NULL, 'DE', 1130, 1, NOW(), NOW(), NULL),
('Artega', 'アルテガ', 'アルテガ', 'ARTEGA01', NULL, NULL, 'DE', 1140, 1, NOW(), NOW(), NULL),
('Mini', 'ミニ', 'MINI', 'MINI01', NULL, NULL, 'DE', 1150, 1, NOW(), NOW(), NULL),

-- アメリカ車（US） 2000番台
('Cadillac', 'キャデラック', 'キャデラック', 'CADILLAC01', NULL, NULL, 'US', 2000, 1, NOW(), NOW(), NULL),
('Chevrolet', 'シボレー', 'シボレー', 'CHEVROLET01', NULL, NULL, 'US', 2010, 1, NOW(), NOW(), NULL),
('Buick', 'ビュイック', 'ビュイック', 'BUICK01', NULL, NULL, 'US', 2020, 1, NOW(), NOW(), NULL),
('Pontiac', 'ポンテアック', 'ポンテアック', 'PONTIAC01', NULL, NULL, 'US', 2030, 1, NOW(), NOW(), NULL),
('Saturn', 'サターン', 'サターン', 'SATURN01', NULL, NULL, 'US', 2040, 1, NOW(), NOW(), NULL),
('Hummer', 'ハマー', 'ハマー', 'HAMMER01', NULL, NULL, 'US', 2050, 1, NOW(), NOW(), NULL),
('GMC', 'ジーエムシー', 'GMC', 'GMC01', NULL, NULL, 'US', 2060, 1, NOW(), NOW(), NULL),
('Ford', 'フォード', 'フォード', 'FORD01', NULL, NULL, 'US', 2070, 1, NOW(), NOW(), NULL),
('Lincoln', 'リンカーン', 'リンカーン', 'LINCOLN01', NULL, NULL, 'US', 2080, 1, NOW(), NOW(), NULL),
('Mercury', 'マーキュリー', 'マーキュリー', 'MERCURY01', NULL, NULL, 'US', 2090, 1, NOW(), NOW(), NULL),
('Chrysler', 'クライスラー', 'クライスラー', 'CHRYSLER01', NULL, NULL, 'US', 2100, 1, NOW(), NOW(), NULL),
('Dodge', 'ダッジ', 'ダッジ', 'DODGE01', NULL, NULL, 'US', 2110, 1, NOW(), NOW(), NULL),
('Plymouth', 'プリムス', 'プリムス', 'PLYMOUTH01', NULL, NULL, 'US', 2120, 1, NOW(), NOW(), NULL),
('AMC', 'エーエムシー', 'AMC', 'AMC01', NULL, NULL, 'US', 2130, 1, NOW(), NOW(), NULL),
('AMCJeep', 'AMCジープ', 'AMCジープ', 'AMCJEEP01', NULL, NULL, 'US', 2140, 1, NOW(), NOW(), NULL),
('Jeep', 'ジープ', 'ジープ', 'JEEP01', NULL, NULL, 'US', 2150, 1, NOW(), NOW(), NULL),
('Oldsmobile', 'オールズモビル', 'オールズモビル', 'OLDS01', NULL, NULL, 'US', 2160, 1, NOW(), NOW(), NULL),
('Winnebago', 'ウィネベーゴ', 'ウィネベーゴ', 'WINNEBAGO01', NULL, NULL, 'US', 2170, 1, NOW(), NOW(), NULL),
('DMC', 'ディーエムシー', 'DMC', 'DMC01', NULL, NULL, 'US', 2180, 1, NOW(), NOW(), NULL),
('Tesla', 'テスラ', 'テスラ', 'TESLA01', NULL, NULL, 'US', 2190, 1, NOW(), NOW(), NULL),
('USLexus', '米国レクサス', 'レクサス（米国）', 'USLEXUS01', NULL, NULL, 'US', 2200, 1, NOW(), NOW(), NULL),
('USInfiniti', '米国インフィニティ', 'インフィニティ（米国）', 'USINFINITI01', NULL, NULL, 'US', 2210, 1, NOW(), NOW(), NULL),
('USAcura', '米国アキュラ', 'アキュラ（米国）', 'USACURA01', NULL, NULL, 'US', 2220, 1, NOW(), NOW(), NULL),
('USToyota', '米国トヨタ', 'トヨタ（米国）', 'USTOYOTA01', NULL, NULL, 'US', 2230, 1, NOW(), NOW(), NULL),
('USScion', '米国サイオン', 'サイオン（米国）', 'USSCION01', NULL, NULL, 'US', 2240, 1, NOW(), NOW(), NULL),
('USNissan', '米国日産', '日産（米国）', 'USNISSAN01', NULL, NULL, 'US', 2250, 1, NOW(), NOW(), NULL),
('USHonda', '米国ホンダ', 'ホンダ（米国）', 'USHONDA01', NULL, NULL, 'US', 2260, 1, NOW(), NOW(), NULL),
('USMazda', '米国マツダ', 'マツダ（米国）', 'USMAZDA01', NULL, NULL, 'US', 2270, 1, NOW(), NOW(), NULL),
('USSubaru', '米国スバル', 'スバル（米国）', 'USSUBARU01', NULL, NULL, 'US', 2280, 1, NOW(), NOW(), NULL),
('USSuzuki', '米国スズキ', 'スズキ（米国）', 'USSUZUKI01', NULL, NULL, 'US', 2290, 1, NOW(), NOW(), NULL),
('USMitsubishi', '米国三菱', '三菱（米国）', 'USMITSUBISHI01', NULL, NULL, 'US', 2300, 1, NOW(), NOW(), NULL),

-- カナダ車（CA）3000番台
('CanadaHonda', 'カナダホンダ', 'カナダホンダ', 'CANADAHONDA01', NULL, NULL, 'CA', 3000, 1, NOW(), NOW(), NULL),

-- イギリス車（GB）4000番台
('Rolls-Royce', 'ロールスロイス', 'ロールスロイス', 'ROLLS01', NULL, NULL, 'GB', 4000, 1, NOW(), NOW(), NULL),
('Bentley', 'ベントレー', 'ベントレー', 'BENTLEY01', NULL, NULL, 'GB', 4010, 1, NOW(), NOW(), NULL),
('Jaguar', 'ジャガー', 'ジャガー', 'JAGUAR01', NULL, NULL, 'GB', 4020, 1, NOW(), NOW(), NULL),
('Daimler', 'デイムラー', 'デイムラー', 'DAIMLER01', NULL, NULL, 'GB', 4030, 1, NOW(), NOW(), NULL),
('LandRover', 'ランドローバー', 'ランドローバー', 'LANDROVER01', NULL, NULL, 'GB', 4040, 1, NOW(), NOW(), NULL),
('AstonMartin', 'アストンマーティン', 'アストンマーティン', 'ASTON01', NULL, NULL, 'GB', 4050, 1, NOW(), NOW(), NULL),
('Lotus', 'ロータス', 'ロータス', 'LOTUS01', NULL, NULL, 'GB', 4060, 1, NOW(), NOW(), NULL),
('LondonTaxi', 'ロンドンタクシー', 'ロンドンタクシー', 'LONTAXI01', NULL, NULL, 'GB', 4070, 1, NOW(), NOW(), NULL),
('McLaren', 'マクラーレン', 'マクラーレン', 'MCLAREN01', NULL, NULL, 'GB', 4080, 1, NOW(), NOW(), NULL),
('MG', 'エムジー', 'MG', 'MG01', NULL, NULL, 'GB', 4090, 1, NOW(), NOW(), NULL),
('Rover', 'ローバー', 'ローバー', 'ROVER01', NULL, NULL, 'GB', 4100, 1, NOW(), NOW(), NULL),
('Austin', 'オースチン', 'オースチン', 'AUSTIN01', NULL, NULL, 'GB', 4110, 1, NOW(), NOW(), NULL),
('Morris', 'モーリス', 'モーリス', 'MORRIS01', NULL, NULL, 'GB', 4120, 1, NOW(), NOW(), NULL),
('BL', 'ビーエル', 'BL', 'BL01', NULL, NULL, 'GB', 4130, 1, NOW(), NOW(), NULL),
('Moke', 'モーク', 'モーク', 'MOKE01', NULL, NULL, 'GB', 4140, 1, NOW(), NOW(), NULL),
('Marcos', 'マーコス', 'マーコス', 'MARCOS01', NULL, NULL, 'GB', 4150, 1, NOW(), NOW(), NULL),
('VandenPlas', 'バンデンプラ', 'バンデンプラ', 'VANDEN01', NULL, NULL, 'GB', 4160, 1, NOW(), NOW(), NULL),
('Woolsey', 'ウーズレイ', 'ウーズレイ', 'WOOLSEY01', NULL, NULL, 'GB', 4170, 1, NOW(), NOW(), NULL),
('Riley', 'ライレー', 'ライレー', 'RILEY01', NULL, NULL, 'GB', 4180, 1, NOW(), NOW(), NULL),
('Caterham', 'ケータハム', 'ケータハム', 'CATERHAM01', NULL, NULL, 'GB', 4190, 1, NOW(), NOW(), NULL),
('Westfield', 'ウエストフィールド', 'ウエストフィールド', 'WESTFIELD01', NULL, NULL, 'GB', 4200, 1, NOW(), NOW(), NULL),
('Morgan', 'モーガン', 'モーガン', 'MORGAN01', NULL, NULL, 'GB', 4210, 1, NOW(), NOW(), NULL),
('Panther', 'パンサー', 'パンサー', 'PANTHER01', NULL, NULL, 'GB', 4220, 1, NOW(), NOW(), NULL),
('Triumph', 'トライアンフ', 'トライアンフ', 'TRIUMPH01', NULL, NULL, 'GB', 4230, 1, NOW(), NOW(), NULL),
('Healey', 'ヒーレー', 'ヒーレー', 'HEALEY01', NULL, NULL, 'GB', 4240, 1, NOW(), NOW(), NULL),
('Ginetta', 'ジネッタ', 'ジネッタ', 'GINETTA01', NULL, NULL, 'GB', 4250, 1, NOW(), NOW(), NULL),
('Vauxhall', 'ボクスホール', 'ボクスホール', 'VAUXHALL01', NULL, NULL, 'GB', 4260, 1, NOW(), NOW(), NULL),

-- スウェーデン車（SE） 5000番台
('Volvo', 'ボルボ', 'ボルボ', 'VOLVO01', NULL, NULL, 'SE', 5000, 1, NOW(), NOW(), NULL),
('Saab', 'サーブ', 'サーブ', 'SAAB01', NULL, NULL, 'SE', 5010, 1, NOW(), NOW(), NULL),
('Koenigsegg', 'ケーニッグゼグ', 'ケーニッグゼグ', 'KOENIGSEGG01', NULL, NULL, 'SE', 5020, 1, NOW(), NOW(), NULL),
('Scania', 'スカニア', 'スカニア', 'SCANIA01', NULL, NULL, 'SE', 5030, 1, NOW(), NOW(), NULL),

-- フランス車（FR）6000番台
('Peugeot', 'プジョー', 'プジョー', 'PEUGEOT01', NULL, NULL, 'FR', 6000, 1, NOW(), NOW(), NULL),
('Renault', 'ルノー', 'ルノー', 'RENAULT01', NULL, NULL, 'FR', 6010, 1, NOW(), NOW(), NULL),
('Citroen', 'シトロエン', 'シトロエン', 'CITROEN01', NULL, NULL, 'FR', 6020, 1, NOW(), NOW(), NULL),
('DSAutomobiles', 'ディーエスオートモビル', 'DSオートモビル', 'DS01', NULL, NULL, 'FR', 6030, 1, NOW(), NOW(), NULL),
('MVS', 'エムブイエス', 'MVS', 'MVS01', NULL, NULL, 'FR', 6040, 1, NOW(), NOW(), NULL),
('Bugatti', 'ブガッティ', 'ブガッティ', 'BUGATTI01', NULL, NULL, 'FR', 6050, 1, NOW(), NOW(), NULL),
('Alpine', 'アルピーヌ', 'アルピーヌ', 'ALPINE01', NULL, NULL, 'FR', 6060, 1, NOW(), NOW(), NULL),

-- イタリア車（IT）7000番台
('Fiat', 'フィアット', 'フィアット', 'FIAT01', NULL, NULL, 'IT', 7000, 1, NOW(), NOW(), NULL),
('AlfaRomeo', 'アルファロメオ', 'アルファロメオ', 'ALFAROMEO01', NULL, NULL, 'IT', 7010, 1, NOW(), NOW(), NULL),
('Ferrari', 'フェラーリ', 'フェラーリ', 'FERRARI01', NULL, NULL, 'IT', 7020, 1, NOW(), NOW(), NULL),
('Lamborghini', 'ランボルギーニ', 'ランボルギーニ', 'LAMBORGHINI01', NULL, NULL, 'IT', 7030, 1, NOW(), NOW(), NULL),
('Maserati', 'マセラティ', 'マセラティ', 'MASERATI01', NULL, NULL, 'IT', 7040, 1, NOW(), NOW(), NULL),
('Lancia', 'ランチア', 'ランチア', 'LANCIA01', NULL, NULL, 'IT', 7050, 1, NOW(), NOW(), NULL),
('Bertone', 'ベルトーネ', 'ベルトーネ', 'BERTONE01', NULL, NULL, 'IT', 7060, 1, NOW(), NOW(), NULL),
('Autobianchi', 'アウトビアンキ', 'アウトビアンキ', 'AUTOBIANCHI01', NULL, NULL, 'IT', 7070, 1, NOW(), NOW(), NULL),
('Abarth', 'アバルト', 'アバルト', 'ABARTH01', NULL, NULL, 'IT', 7080, 1, NOW(), NOW(), NULL),
('Innocenti', 'イノチェンティ', 'イノチェンティ', 'INNOCENTI01', NULL, NULL, 'IT', 7090, 1, NOW(), NOW(), NULL),
('DeTomaso', 'デトマソ', 'デトマソ', 'DETOMASO01', NULL, NULL, 'IT', 7100, 1, NOW(), NOW(), NULL),

-- オーストリア (AT) 7500番台
('KTM', 'ケーティーエム', 'KTM', 'KTM01', NULL, NULL, 'AT', 7500, 1, NOW(), NOW(), NULL),

-- スペイン (ES) 7600番台  
('SEAT', 'シート', 'SEAT', 'SEAT01', NULL, NULL, 'ES', 7600, 1, NOW(), NOW(), NULL),

-- スロベニア (SI) 7700番台
('Adria', 'アドリア', 'アドリア', 'ADRIA01', NULL, NULL, 'SI', 7700, 1, NOW(), NOW(), NULL),

-- ロシア (RU) 7800番台
('Lada', 'ラーダ', 'ラーダ', 'LADA01', NULL, NULL, 'RU', 7800, 1, NOW(), NOW(), NULL),

-- 中国 (CN) 7900番台
('BYD', 'ビーワイディー', 'BYD', 'BYD01', NULL, NULL, 'CN', 7900, 1, NOW(), NOW(), NULL),

-- 韓国 (KR) 8000番台
('Hyundai', 'ヒョンデ', 'ヒュンダイ', 'HYUNDAI01', NULL, NULL, 'KR', 8000, 1, NOW(), NOW(), NULL),
('Kia', '起亜', 'キア', 'KIA01', NULL, NULL, 'KR', 8010, 1, NOW(), NOW(), NULL),

-- マレーシア (MY) 8100番台
('TD', 'ティーディー', 'TD', 'TD01', NULL, NULL, 'MY', 8100, 1, NOW(), NOW(), NULL),

-- 南アフリカ (ZA) 8200番台
('Barkin', 'バーキン', 'バーキン', 'BARKIN01', NULL, NULL, 'ZA', 8200, 1, NOW(), NOW(), NULL),

-- 輸入車その他 (XX) 9999番台
('輸入車その他', 'ユウニュウシャソノタ', '輸入車その他', 'IMPORTETC01', NULL, NULL, 'XX', 9999, 1, NOW(), NOW(), NULL);
