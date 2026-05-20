<?php

declare(strict_types=1);

namespace App\Domain\CarUpload\Services;

use App\Constants\CarStatus;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\Mst\MstVehicles;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkDealerFee;

class BulkCarValidatorService
{
    // 必須カラム
    private const REQUIRED_COLUMNS = [
        '操作',
        'ユニークID',
        'メーカー',
        '車体名',
        '年式・グレード',
        '画像フォルダ名',
        '年式',
        '支払価格（円）',
        '価格表示方法',
        '走行距離（km）',
        'ボディカラー',
        '色系統',
        '修復歴',
        '地域（都道府県）',
        '車検状態',
        'ハンドル',
        'ドア数',
        'スライドドア',
    ];

    // ENUMバリデーション定義
    private const ENUM_COLUMNS = [
        '価格表示方法'  => ['actual（実際の価格）', 'negotiable（要相談）', 'ask（応相談）'],
        '色系統'        => ['white（白系）', 'black（黒系）', 'silver（銀系）', 'red（赤系）', 'blue（青系）', 'green（緑系）', 'brown（茶系）', 'yellow（黄系）', 'pink（ピンク系）', 'other（その他）'],
        '修復歴'        => ['none（なし）', 'minor（軽微）', 'major（修復歴あり）', 'unknown（不明）'],
        '燃料タイプ'    => ['gasoline（ガソリン）', 'diesel（ディーゼル）', 'hybrid（ハイブリッド）', 'electric（電気）', 'phev（PHEV）', 'other（その他）'],
        '車検状態'      => ['available（車検あり）', 'none（車検なし）', 'new_car（新車）'],
        'ハンドル'      => ['right（右ハンドル）', 'left（左ハンドル）'],
        'スライドドア'  => ['none（なし）', 'right_only（右のみ）', 'both_manual（両側手動）', 'both_power（両側電動）', 'right_power（右電動）', 'left_power（左電動）'],
        '駆動方式'      => ['2WD', '4WD', 'AWD', 'FR', 'FF', 'MR', 'RR'],
        'ミッション'    => ['AT', 'MT', 'CVT', 'DCT', 'other（その他）'],
    ];

    // 数値カラム
    private const NUMERIC_COLUMNS = [
        '支払価格（円）'      => ['required' => true,  'min' => 0],
        '走行距離（km）'      => ['required' => true,  'min' => 0],
        'リサイクル預託金（円）' => ['required' => false, 'min' => 0],
        '排気量（cc）'        => ['required' => false, 'min' => 0],
        'ドア数'              => ['required' => true,  'min' => 2, 'max' => 7],
        '乗車定員'            => ['required' => false, 'min' => 1, 'max' => 99],
    ];

    // ○系カラム（装備・特徴）
    private const CIRCLE_COLUMNS = [
        'ワンオーナー', 'キャンピングカー', '福祉車両', '登録済未使用車',
        'エコカー減税対象', '未登録車', '車両品質評価書付き', '購入プラン付き',
        'アフター保証対象車', 'オンライン相談可', 'ローン可',
        'ABS', '運転席エアバッグ', 'サポカー', '助手席エアバッグ',
        '衝突被害軽減ブレーキ', 'サイドエアバッグ', 'クルーズコントロール',
        'カーテンエアバッグ', 'アダプティブクルーズコントロール', '頭部衝撃緩和ヘッドレスト',
        'レーンキープアシスト', 'フロントカメラ', 'パーキングアシスト', 'サイドカメラ',
        'アクセル踏み間違い防止装置', 'バックカメラ', '横滑り防止装置', '全周囲カメラ',
        '障害物センサー', 'ブラインドスポットモニター', 'リアトラフィックモニタ', 'ヒルディセントコントロール',
        'キーレスエントリー', 'ETC', 'スマートキー', '盗難防止装置', 'パワーウィンドウ',
        'サンルーフ・ガラスルーフ', 'パワステ', 'ルーフレール', 'エアコン・クーラー',
        '後席モニター', 'Wエアコン', 'エアサスペンション', 'ディスチャージヘッドランプ',
        '1500W給電', 'フロントフォグランプ', 'ドライブレコーダー', 'オートマチックハイビーム',
        '電動開閉バックドア', 'LEDヘッドライト', 'ディスプレイオーディオ', 'アダプティブヘッドライト',
        'フルフラットシート', '本革シート', '3列シート', 'ベンチシート', 'ウォークスルー',
        '電動シート', 'シートヒーター', 'オットマン', 'シートエアコン',
        'フルエアロ', 'ローダウン', 'アルミホイール', '全塗装済', 'リフトアップ',
        'アイドリングストップ', 'エコカー減税対象車',
        'CD再生', 'DVD再生', 'Bluetooth', 'USB', 'カーナビあり', 'TVあり', 'DVDナビあり',
    ];

    /**
     * xlsxから読み込んだデータをバリデーション
     *
     * @param  array $rows    行データ配列（連想配列）
     * @param  int   $dealerId
     * @return array ['errors' => [...], 'valid' => bool]
     */
    public function validate(array $rows, int $dealerId): array
    {
        $errors = [];

        if (empty($rows)) {
            return ['errors' => [['row' => '-', 'column' => '-', 'message' => 'データが1件もありません。']], 'valid' => false];
        }

        $headers = array_keys($rows[0]);
        foreach (self::REQUIRED_COLUMNS as $col) {
            if (!in_array($col, $headers)) {
                $errors[] = ['row' => 'ヘッダー', 'column' => $col, 'message' => '必須列が見つかりません。'];
            }
        }

        if (!empty($errors)) {
            return ['errors' => $errors, 'valid' => false];
        }

        $manufacturers    = MstManufacturers::where('is_active', 1)->pluck('id', 'display_name')->toArray();
        $regions          = MstRegions::pluck('id', 'name')->toArray();
        $bodyTypes        = MstBodyTypes::pluck('id', 'name')->toArray();
        $uniqueIdsInSheet = [];

        foreach ($rows as $rowIndex => $row) {
            $rowNum    = $rowIndex + 3;
            $operation = trim($row['操作'] ?? '');

            // ===== 操作列チェック =====
            $validOperations = array_values(\App\Constants\FileStatus::LABELS);
            if (empty($operation)) {
                $errors[] = $this->error($rowNum, '操作', '必須項目です。新規・更新・削除のいずれかを入力してください。');
            } elseif (!in_array($operation, $validOperations)) {
                $errors[] = $this->error($rowNum, '操作', "操作の値が正しくありません：{$operation}。新規・更新・削除のいずれかを入力してください。");
            }

            // ===== ユニークID =====
            $uniqueId = trim($row['ユニークID'] ?? '');
            if (empty($uniqueId)) {
                $errors[] = $this->error($rowNum, 'ユニークID', '必須項目です。');
            } else {
                if (in_array($uniqueId, $uniqueIdsInSheet)) {
                    $errors[] = $this->error($rowNum, 'ユニークID', "シート内に重複があります：{$uniqueId}");
                }
                $uniqueIdsInSheet[] = $uniqueId;

                // 更新・削除はDBに存在するか確認
                if (in_array($operation, ['更新', '削除'])) {
                    $existing = StkCar::where('bulk_upload_key', $uniqueId)->first();
                    if (!$existing) {
                        $errors[] = $this->error($rowNum, 'ユニークID', "更新・削除対象が見つかりません：{$uniqueId}");
                    }
                }

                // 新規はavailable/reservedとの重複チェック
                if ($operation === '新規') {
                    $existing = StkCar::where('bulk_upload_key', $uniqueId)->first();
                    if ($existing) {
                        $blockedStatuses = [CarStatus::AVAILABLE, CarStatus::RESERVED];
                        if (in_array($existing->status, $blockedStatuses)) {
                            $statusLabel = CarStatus::LABELS[$existing->status];
                            $errors[] = $this->error($rowNum, 'ユニークID', "このIDは{$statusLabel}の車両に使用されています：{$uniqueId}");
                        }
                    }
                }
            }

            // ===== 画像フォルダ名 =====
            if ($operation !== '削除' && empty(trim($row['画像フォルダ名'] ?? ''))) {
                $errors[] = $this->error($rowNum, '画像フォルダ名', '必須項目です。');
            }

            // ===== 削除の場合はここで次の行へ =====
            if ($operation === '削除') {
                continue;
            }

            // ===== 必須項目チェック =====
            foreach (self::REQUIRED_COLUMNS as $col) {
                if (in_array($col, ['操作', 'ユニークID', '画像フォルダ名'])) continue;
                if (empty(trim((string)($row[$col] ?? '')))) {
                    $errors[] = $this->error($rowNum, $col, '必須項目です。');
                }
            }

            // ===== ENUMチェック =====
            foreach (self::ENUM_COLUMNS as $col => $allowed) {
                $val = trim((string)($row[$col] ?? ''));
                if (empty($val)) continue;
                if (!in_array($val, $allowed)) {
                    $errors[] = $this->error($rowNum, $col, "選択肢が正しくありません：{$val}");
                }
            }

            // ===== 数値チェック =====
            foreach (self::NUMERIC_COLUMNS as $col => $config) {
                $val = trim((string)($row[$col] ?? ''));
                if (!$config['required'] && empty($val)) continue;
                if ($config['required'] && empty($val)) {
                    $errors[] = $this->error($rowNum, $col, '必須項目です。');
                    continue;
                }
                if (!is_numeric($val) || (int)$val < 0) {
                    $errors[] = $this->error($rowNum, $col, '0以上の整数を入力してください。');
                    continue;
                }
                if (isset($config['min']) && (int)$val < $config['min']) {
                    $errors[] = $this->error($rowNum, $col, "{$config['min']}以上の値を入力してください。");
                }
                if (isset($config['max']) && (int)$val > $config['max']) {
                    $errors[] = $this->error($rowNum, $col, "{$config['max']}以下の値を入力してください。");
                }
            }

            // ===== 年式チェック =====
            $modelYear = trim((string)($row['年式'] ?? ''));
            if (!empty($modelYear)) {
                if (!is_numeric($modelYear) || (int)$modelYear < 1900 || (int)$modelYear > now()->year) {
                    $errors[] = $this->error($rowNum, '年式', '1900年〜' . now()->year . '年の範囲で入力してください。');
                }
            }

            // ===== 日付チェック =====
            foreach (['初回登録日', '車検満了日'] as $col) {
                $val = trim((string)($row[$col] ?? ''));
                if (empty($val)) continue;
                if (!\DateTime::createFromFormat('Y-m-d', $val)) {
                    $errors[] = $this->error($rowNum, $col, 'YYYY-MM-DD形式で入力してください。例：2024-03-15');
                }
            }

            // ===== ○系チェック =====
            foreach (self::CIRCLE_COLUMNS as $col) {
                if (!array_key_exists($col, $row)) continue;
                $val = trim((string)($row[$col] ?? ''));
                if (!empty($val) && $val !== '○') {
                    $errors[] = $this->error($rowNum, $col, '「○」または空白で入力してください。');
                }
            }

            // ===== マスタ存在チェック =====
            $manufacturer = trim((string)($row['メーカー'] ?? ''));
            if (!empty($manufacturer) && !isset($manufacturers[$manufacturer])) {
                $errors[] = $this->error($rowNum, 'メーカー', "マスタに存在しないメーカーです：{$manufacturer}");
            }

            $region = trim((string)($row['地域（都道府県）'] ?? ''));
            if (!empty($region) && !isset($regions[$region])) {
                $errors[] = $this->error($rowNum, '地域（都道府県）', "マスタに存在しない地域です：{$region}");
            }

            $bodyType = trim((string)($row['ボディタイプ'] ?? ''));
            if (!empty($bodyType) && !isset($bodyTypes[$bodyType])) {
                $errors[] = $this->error($rowNum, 'ボディタイプ', "マスタに存在しないボディタイプです：{$bodyType}");
            }

            // ===== その他オプションチェック =====
            $validCategories = ['basic', 'safety', 'environmental', 'audio', 'navigation', 'dress_up', 'seat', 'other'];
            for ($i = 1; $i <= 5; $i++) {
                $catKey  = "その他オプション{$i}カテゴリ";
                $nameKey = "その他オプション{$i}名称";
                $cat     = trim((string)($row[$catKey] ?? ''));
                $name    = trim((string)($row[$nameKey] ?? ''));

                if (!empty($name) && empty($cat)) {
                    $errors[] = $this->error($rowNum, $catKey, "オプション{$i}名称が入力されていますがカテゴリが未選択です。");
                }
                if (!empty($cat)) {
                    $catValue = explode('（', $cat)[0];
                    if (!in_array($catValue, $validCategories)) {
                        $errors[] = $this->error($rowNum, $catKey, "カテゴリの選択肢が正しくありません：{$cat}");
                    }
                }
            }
        }

        return [
            'errors' => $errors,
            'valid'  => empty($errors),
        ];
    }

    /**
     * 画像フォルダとxlsxの突き合わせバリデーション
     *
     * @param  array $rows          行データ配列
     * @param  array $uploadedFiles アップロードされたファイル名一覧
     * @return array ['errors' => [...], 'valid' => bool]
     */
    public function validateImages(array $rows, array $uploadedFiles): array
    {
        $errors = [];

        // 削除行は除外してフォルダ名一覧を取得
        $folderNames = collect($rows)
            ->filter(fn ($row) => trim($row['操作'] ?? '') !== '削除')
            ->pluck('画像フォルダ名')
            ->filter()
            ->map(fn ($v) => trim((string)$v))
            ->unique()
            ->values()
            ->toArray();

        // アップロードされたファイルからフォルダ名を抽出
        $uploadedFolders = collect($uploadedFiles)
            ->map(function ($path) {
                $parts = explode('/', $path);
                return $parts[count($parts) - 2] ?? '';
            })
            ->unique()
            ->values()
            ->toArray();

        // xlsxに必要なフォルダが揃っているかチェック（逆方向はチェックしない）
        foreach ($folderNames as $folderName) {
            if (!in_array($folderName, $uploadedFolders)) {
                $errors[] = [
                    'folder'  => $folderName,
                    'message' => "画像フォルダが見つかりません：{$folderName}",
                ];
            }
        }

        // アップロードされたがxlsxにないフォルダはチェックしない（削除行対応）

        return [
            'errors' => $errors,
            'valid'  => empty($errors),
        ];
    }

    private function error(int $row, string $column, string $message): array
    {
        return [
            'row'     => $row,
            'column'  => $column,
            'message' => $message,
        ];
    }
}