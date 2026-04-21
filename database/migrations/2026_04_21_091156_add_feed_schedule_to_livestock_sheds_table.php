<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('livestock_sheds', function (Blueprint $table) {
            $table->string('feed_schedule')->nullable()->after('last_cleaned_at');
        });
    }

    public function down(): void
    {
        Schema::table('livestock_sheds', function (Blueprint $table) {
            $table->dropColumn('feed_schedule');
        });
    }
};
