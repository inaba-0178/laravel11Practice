<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'mst_backup';

    private const BACKUP_TABLES = [
        'mst_areas' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `name` varchar(50) NOT NULL,
            `query_param` varchar(50) NOT NULL,
            `sort_order` int(11) NOT NULL DEFAULT 0,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_basic_options' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `value` varchar(50) NOT NULL,
            `label` varchar(100) NOT NULL,
            `is_highlight` tinyint(1) NOT NULL DEFAULT 0,
            `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_body_types' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `name_kana` varchar(255) NULL DEFAULT NULL,
            `code` varchar(50) NOT NULL,
            `description` text NULL DEFAULT NULL,
            `available_countries` longtext NOT NULL DEFAULT \'["JP"]\',
            `sort_order` int(10) unsigned NOT NULL DEFAULT 1000,
            `is_active` tinyint(4) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_body_type_images' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `body_type_id` bigint(20) unsigned NOT NULL,
            `image_type` varchar(50) NOT NULL DEFAULT \'logo\',
            `file_path` varchar(500) NOT NULL,
            `alt_text` varchar(255) NULL DEFAULT NULL,
            `sort_order` int(10) unsigned NOT NULL DEFAULT 1000,
            `is_main` tinyint(4) NOT NULL DEFAULT 0,
            `is_active` tinyint(4) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_car_series' => '
            `series_id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `series_name` varchar(255) NOT NULL,
            `manufacturer_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `series_id`)
        ',
        'mst_car_series_body_types' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `series_id` bigint(20) unsigned NOT NULL,
            `body_type_id` bigint(20) unsigned NOT NULL,
            `is_primary` tinyint(4) NULL DEFAULT 0,
            `sort_order` int(11) NULL DEFAULT 1000,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_car_type_options' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `value` varchar(50) NOT NULL,
            `label` varchar(100) NOT NULL,
            `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_color_options' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `value` varchar(50) NOT NULL,
            `label` varchar(100) NOT NULL,
            `hex_code` varchar(7) NULL DEFAULT NULL,
            `group` varchar(20) NULL DEFAULT NULL,
            `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_detail_options' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `value` varchar(50) NOT NULL,
            `label` varchar(100) NOT NULL,
            `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_displacement_lists' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `min_amount` decimal(10,0) NULL DEFAULT NULL,
            `max_amount` decimal(10,0) NULL DEFAULT NULL,
            `is_unlimited` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_equipment_basic' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `value` varchar(50) NOT NULL,
            `label` varchar(100) NOT NULL,
            `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_equipment_dressup' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `value` varchar(50) NOT NULL,
            `label` varchar(100) NOT NULL,
            `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_equipment_env' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `value` varchar(50) NOT NULL,
            `label` varchar(100) NOT NULL,
            `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_equipment_safety' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `value` varchar(50) NOT NULL,
            `label` varchar(100) NOT NULL,
            `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_featured_body_types' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `body_type_code` varchar(50) NOT NULL,
            `position` varchar(20) NOT NULL,
            `sort_order` int(10) unsigned NOT NULL DEFAULT 1000,
            `is_active` tinyint(4) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_featured_brands' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `manufacturer_code` varchar(50) NOT NULL,
            `position` varchar(20) NOT NULL,
            `sort_order` int(10) unsigned NOT NULL DEFAULT 1000,
            `is_active` tinyint(4) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_liability_insurances' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `vehicle_type` enum(\'light\',\'standard\') NOT NULL,
            `months` int(10) unsigned NOT NULL,
            `amount` decimal(10,0) NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_loan_plans' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `interest_rate` decimal(5,2) NOT NULL,
            `months_options` longtext NOT NULL,
            `min_months` int(10) unsigned NOT NULL,
            `max_months` int(10) unsigned NOT NULL,
            `is_default` tinyint(4) NOT NULL DEFAULT 0,
            `is_active` tinyint(4) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_manufacturers' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `name_kana` varchar(255) NULL DEFAULT NULL,
            `display_name` varchar(255) NULL DEFAULT NULL,
            `code` varchar(50) NOT NULL,
            `url` varchar(500) NULL DEFAULT NULL,
            `description` text NULL DEFAULT NULL,
            `country_code` char(2) NULL DEFAULT NULL,
            `sort_order` int(10) unsigned NOT NULL DEFAULT 1000,
            `is_active` tinyint(4) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_manufacturer_images' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `manufacturer_id` bigint(20) unsigned NOT NULL,
            `image_type` varchar(50) NOT NULL DEFAULT \'logo\',
            `file_path` varchar(500) NOT NULL,
            `alt_text` varchar(255) NULL DEFAULT NULL,
            `sort_order` int(10) unsigned NOT NULL DEFAULT 1000,
            `is_main` tinyint(4) NOT NULL DEFAULT 0,
            `is_active` tinyint(4) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_mileage_lists' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `min_amount` decimal(10,0) NULL DEFAULT NULL,
            `max_amount` decimal(10,0) NULL DEFAULT NULL,
            `is_unlimited` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_price_lists' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `max_amount` decimal(10,0) NULL DEFAULT NULL,
            `is_unlimited` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_regions' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `area_code` int(11) NULL DEFAULT NULL,
            `name` varchar(255) NOT NULL,
            `url` varchar(255) NULL DEFAULT NULL,
            `query_param` varchar(255) NULL DEFAULT NULL,
            `sort_order` int(11) NOT NULL DEFAULT 0,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_riding_capacity_lists' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `max_amount` decimal(2,0) NOT NULL,
            `is_unlimited` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_seat_options' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `value` varchar(50) NOT NULL,
            `label` varchar(100) NOT NULL,
            `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_vehicle_weight_taxes' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `weight_from` int(10) unsigned NOT NULL,
            `weight_to` int(10) unsigned NOT NULL,
            `is_light` tinyint(1) NOT NULL DEFAULT 0,
            `amount` decimal(10,0) NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_vehicles' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `series_id` bigint(20) unsigned NOT NULL,
            `manufacturer_id` bigint(20) unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `model_code` varchar(50) NULL DEFAULT NULL,
            `body_type` varchar(50) NULL DEFAULT NULL,
            `country_code` char(2) NULL DEFAULT NULL,
            `status` enum(\'active\',\'discontinued\',\'concept\') NOT NULL DEFAULT \'active\',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
        'mst_vehicle_year_versions' => '
            `id` bigint(20) unsigned NOT NULL,
            `version_id` bigint(20) unsigned NOT NULL,
            `vehicle_id` bigint(20) unsigned NOT NULL,
            `year_from` year(4) NOT NULL,
            `year_to` year(4) NULL DEFAULT NULL,
            `displacement_cc` int(10) unsigned NULL DEFAULT NULL,
            `drive_type` enum(\'2WD\',\'4WD\',\'FR\',\'FF\') NOT NULL DEFAULT \'2WD\',
            `fuel_efficiency_from` decimal(5,1) NULL DEFAULT NULL,
            `fuel_efficiency_to` decimal(5,1) NULL DEFAULT NULL,
            `max_power_kw` int(10) unsigned NULL DEFAULT NULL,
            `transmission_type` enum(\'AT\',\'MT\',\'CVT\') NOT NULL DEFAULT \'AT\',
            `weight_kg` int(10) unsigned NULL DEFAULT NULL,
            `price_range_from` decimal(10,0) NULL DEFAULT NULL,
            `price_range_to` decimal(10,0) NULL DEFAULT NULL,
            `is_latest` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`version_id`, `id`)
        ',
    ];

    public function up(): void
    {
        foreach (self::BACKUP_TABLES as $tableName => $columns) {
            $backupTable = $tableName . '_backups';
            DB::connection('mst_backup')->statement("
                CREATE TABLE IF NOT EXISTS `{$backupTable}` (
                    {$columns}
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        foreach (array_keys(self::BACKUP_TABLES) as $tableName) {
            $backupTable = $tableName . '_backups';
            DB::connection('mst_backup')->statement("DROP TABLE IF EXISTS `{$backupTable}`");
        }
    }
};