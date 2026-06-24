<?php

declare(strict_types=1);

namespace App\Application\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    private const TABLE_MAP = [
        'stk_cars'           => 'log_car',
        'stk_car_details'    => 'log_car',
        'stk_car_images'     => 'log_car',
        'mst_countries'      => 'log_mst',
        'mst_basic_options'  => 'log_mst',
        'mst_body_types'     => 'log_mst',
        'mst_manufacturers'  => 'log_mst',
        'mst_car_series'     => 'log_mst',
        'usr_users'          => 'log_member',
        'usr_user_profiles'  => 'log_member',
    ];

    public static function logModel(Model $model, string $action): void
    {
        $tableName = $model->getTable();
        $logTable  = self::TABLE_MAP[$tableName] ?? null;

        if ($logTable === null) {
            return;
        }

        $changedKeys = array_keys($model->getDirty());
        $before      = $action === 'update'
            ? array_intersect_key($model->getOriginal(), array_flip($changedKeys))
            : null;
        $after       = $action === 'delete'
            ? null
            : array_intersect_key($model->getAttributes(), array_flip($changedKeys ?: array_keys($model->getAttributes())));

        self::write($logTable, [
            'action'       => $action,
            'target_table' => $tableName,
            'target_id'    => $model->getKey(),
            'before_data'  => $before ? json_encode($before) : null,
            'after_data'   => $after  ? json_encode($after)  : null,
        ]);
    }

    public static function logAuth(string $action, ?int $userId, ?string $userName, string $operatorType, string $result): void
    {
        self::write('log_auth', [
            'action'  => $action,
            'result'  => $result,
        ], $userId, $userName, $operatorType);
    }

    public static function logUpload(string $targetTable, ?int $targetId, string $filePath): void
    {
        self::write('log_upload', [
            'action'       => 'upload',
            'target_table' => $targetTable,
            'target_id'    => $targetId,
            'after_data'   => json_encode(['path' => $filePath]),
        ]);
    }

    private static function write(string $logTable, array $data, ?int $userId = null, ?string $userName = null, string $operatorType = ''): void
    {
        try {
            [$resolvedType, $resolvedId, $resolvedName] = $operatorType
                ? [$operatorType, $userId, $userName]
                : self::resolveOperator();

            DB::connection('log')->table($logTable)->insert(array_merge([
                'operator_type' => $resolvedType,
                'operator_id'   => $resolvedId,
                'operator_name' => $resolvedName,
                'result'        => 'success',
                'ip_address'    => Request::ip(),
                'user_agent'    => Request::userAgent(),
                'operation_at'  => now(),
                'created_at'    => now(),
            ], $data));
        } catch (\Throwable) {
            // ログ書き込み失敗はアプリを止めない
        }
    }

    private static function resolveOperator(): array
    {
        $request = request();

        foreach (['sanctum', 'members', 'web'] as $guard) {
            $user = $request->user($guard);
            if ($user === null) {
                continue;
            }

            $type = match($guard) {
                'members' => 'member',
                default   => 'admin',
            };

            return [$type, $user->id, $user->name ?? $user->email ?? ''];
        }

        return ['system', null, 'system'];
    }
}
