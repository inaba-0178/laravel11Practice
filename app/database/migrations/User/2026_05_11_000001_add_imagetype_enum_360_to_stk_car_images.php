<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        DB::connection('user')->statement("
            ALTER TABLE stk_car_images 
            MODIFY COLUMN image_type 
            ENUM('exterior','interior','engine','other','360') 
            NOT NULL DEFAULT 'exterior' 
            COMMENT '画像種別'
        ");
    }

    public function down(): void
    {
        DB::connection('user')->statement("
            ALTER TABLE stk_car_images 
            MODIFY COLUMN image_type 
            ENUM('exterior','interior','engine','other') 
            NOT NULL DEFAULT 'exterior' 
            COMMENT '画像種別'
        ");
    }
};