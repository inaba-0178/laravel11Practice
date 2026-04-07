<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection($this->connection)->create('usr_favorite_cars', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 36)->comment('会員ID（UUID）');
            $table->foreignId('car_id')->constrained('stk_cars');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('usr_favorite_cars');
    }
};