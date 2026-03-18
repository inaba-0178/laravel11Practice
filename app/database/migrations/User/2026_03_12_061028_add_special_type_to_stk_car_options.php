<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        DB::connection('user')->statement("
            ALTER TABLE stk_car_options
            MODIFY COLUMN option_category
            ENUM('basic','safety','environmental','audio','navigation','seat','dress_up','special_type','other')
            NOT NULL COMMENT 'オプション種別'
        ");
    }

    public function down(): void
    {
        DB::connection('user')->statement("
            ALTER TABLE stk_car_options
            MODIFY COLUMN option_category
            ENUM('basic','safety','environmental','audio','navigation','seat','dress_up','other')
            NOT NULL COMMENT 'オプション種別'
        ");
    }
};