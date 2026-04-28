<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("livestock_sheds", function (Blueprint $table) {
            $table->unsignedTinyInteger("grid_row")->default(1)->after("status");
            $table->unsignedTinyInteger("grid_col")->default(1)->after("grid_row");
            $table->unsignedTinyInteger("grid_row_span")->default(1)->after("grid_col");
            $table->unsignedTinyInteger("grid_col_span")->default(1)->after("grid_row_span");
        });
    }

    public function down(): void
    {
        Schema::table("livestock_sheds", function (Blueprint $table) {
            $table->dropColumn(["grid_row", "grid_col", "grid_row_span", "grid_col_span"]);
        });
    }
};
