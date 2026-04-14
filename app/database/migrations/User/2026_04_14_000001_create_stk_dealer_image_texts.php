<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_dealer_image_texts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('image_id');
            $table->text('caption')->comment('説明文');
            $table->timestamps();

            $table->foreign('image_id')->references('id')->on('stk_dealer_images')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_dealer_image_texts');
    }
};