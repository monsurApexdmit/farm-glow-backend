<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            if (!Schema::hasColumn('farms', 'farm_type')) {
                $table->string('farm_type')->default('crop')->after('unit');
            }
            if (!Schema::hasColumn('farms', 'soil_type')) {
                $table->string('soil_type')->default('loam')->after('farm_type');
            }
            if (!Schema::hasColumn('farms', 'climate_zone')) {
                $table->string('climate_zone')->default('temperate')->after('soil_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn(['farm_type', 'soil_type', 'climate_zone']);
        });
    }
};
