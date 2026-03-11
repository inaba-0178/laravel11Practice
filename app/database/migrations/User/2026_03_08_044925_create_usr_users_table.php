<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('usr_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sei');
            $table->string('mei');
            $table->string('sei_kana');
            $table->string('mei_kana');
            $table->date('birth_date');
            $table->string('post_code', 7);
            $table->string('prefecture');
            $table->string('city');
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('phone_number', 11);
            $table->tinyInteger('gender')->default(0);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('email_changed_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('usr_users');
    }
};