<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("livestock_sheds", function (Blueprint $table) {
            if (!Schema::hasColumn("livestock_sheds", "height")) {
                $table->decimal("height", 8, 2)->nullable()->after("width");
            }
        });
    }

    public function down(): void
    {
        Schema::table("livestock_sheds", function (Blueprint $table) {
            if (Schema::hasColumn("livestock_sheds", "height")) {
                $table->dropColumn("height");
            }
        });
    }
};
