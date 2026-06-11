<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('rooms', function (Blueprint $table) {
            $table->string('related_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('related_id')->nullable()->change();
        });
    }
};