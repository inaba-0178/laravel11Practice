<?php

declare(strict_types=1);

namespace App\Domain\Mst\Services;

use App\Constants\MstImportOrder;
use App\Infrastructure\Eloquent\Mst\MstVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MstRollbackService
{
    /**
     * ロールバック実行
     */
    public function rollback(MstVersion $targetVersion, string $reason): void
    {
        // テーブル構造チェック
        $structureErrors = $this->checkTableStructures($targetVersion);
        if (!empty($structureErrors)) {
            throw new \RuntimeException(implode("\n", $structureErrors));
        }

        DB::connection('mst')->transaction(function () use ($targetVersion, $reason) {

            // 現在のactiveをarchivedに変更
            MstVersion::where('status', 'active')->update(['status' => 'archived']);

            // 対象バージョンをactiveに変更
            $targetVersion->update([
                'status'          => 'active',
                'activated_by'    => Auth::id(),
                'activated_at'    => now(),
                'rolled_back_by'  => Auth::id(),
                'rolled_back_at'  => now(),
                'rollback_reason' => $reason,
            ]);

            DB::connection('mst')->statement('SET FOREIGN_KEY_CHECKS=0');

            try {
                foreach (MstImportOrder::ORDER as $tableName) {
                    $backupTable = $tableName . '_backups';

                    // バックアップデータ取得
                    $rows = DB::connection('mst_backup')
                        ->table($backupTable)
                        ->where('version_id', $targetVersion->id)
                        ->get()
                        ->toArray();

                    // 本テーブルをクリア
                    DB::connection('mst')->table($tableName)->delete();

                    if (empty($rows)) continue;

                    // バックアップデータを本テーブルに投入
                    $insertData = array_map(function ($row) {
                        $row = (array) $row;
                        $row['created_at'] = now();
                        $row['updated_at'] = now();
                        return $row;
                    }, $rows);

                    foreach (array_chunk($insertData, 1000) as $chunk) {
                        DB::connection('mst')->table($tableName)->insert($chunk);
                    }
                }
            } finally {
                DB::connection('mst')->statement('SET FOREIGN_KEY_CHECKS=1');
            }
        });
    }

    /**
     * テーブル構造チェック
     * バックアップのカラムが現在のテーブルに投入可能か確認する
     */
    private function checkTableStructures(MstVersion $targetVersion): array
    {
        $errors = [];

        foreach (MstImportOrder::ORDER as $tableName) {
            $backupTable = $tableName . '_backups';

            // バックアップにデータがあるか確認
            $backupCount = DB::connection('mst_backup')
                ->table($backupTable)
                ->where('version_id', $targetVersion->id)
                ->count();

            if ($backupCount === 0) continue;

            // 現在のテーブルのカラム情報を取得
            $currentColumns = DB::connection('mst')->select("
                SELECT COLUMN_NAME, IS_NULLABLE, COLUMN_DEFAULT
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
            ", [$tableName]);

            $currentColumnMap = collect($currentColumns)
                ->keyBy('COLUMN_NAME')
                ->toArray();

            // バックアップテーブルのカラム情報を取得
            $backupColumns = DB::connection('mst_backup')->select("
                SELECT COLUMN_NAME, IS_NULLABLE, COLUMN_DEFAULT
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
            ", [$backupTable]);

            $backupColumnNames = collect($backupColumns)
                ->pluck('COLUMN_NAME')
                ->toArray();

            // 現在のテーブルに追加されたカラムでNULL不許可かつデフォルト値なしのものがあればエラー
            foreach ($currentColumnMap as $columnName => $column) {
                if (in_array($columnName, ['created_at', 'updated_at'])) continue;
                if (in_array($columnName, $backupColumnNames)) continue;

                // バックアップに存在しない新しいカラム
                if ($column->IS_NULLABLE === 'NO' && $column->COLUMN_DEFAULT === null) {
                    $errors[] = "テーブル「{$tableName}」：カラム「{$columnName}」がNULL不許可のためロールバックできません。テーブル構造に変更があったため戻すことができません。もしどうしても対象のバージョンに戻したい場合、ロールバック時のテーブル状態に戻してください。";
                }
            }
        }

        return $errors;
    }
}