<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('stk_car_dealers', function (Blueprint $table) {
            $table->decimal('review_rating', 3, 2)->nullable()->default(null)->comment('クチコミ評価平均')->after('is_active');
            $table->unsignedInteger('review_count')->default(0)->comment('クチコミ件数')->after('review_rating');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_car_dealers', function (Blueprint $table) {
            $table->dropColumn(['review_rating', 'review_count']);
        });
    }
};