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
        Schema::connection('user')->table('messages', function (Blueprint $table) {
            $table->string('attachment_url')->nullable()->after('message');
            $table->string('attachment_type')->nullable()->after('attachment_url');
            $table->string('attachment_name')->nullable()->after('attachment_type');
            $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_name');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('messages', function (Blueprint $table) {
            $table->dropColumn([
                'attachment_url',
                'attachment_type',
                'attachment_name',
                'attachment_size',
            ]);
        });
    }
};