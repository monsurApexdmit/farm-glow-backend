<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("crop_health_records", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("crop_id");
            $table->string("health_status");
            $table->integer("disease_count")->default(0);
            $table->text("disease_notes")->nullable();
            $table->integer("pest_count")->default(0);
            $table->text("pest_notes")->nullable();
            $table->integer("weed_count")->default(0);
            $table->text("weed_notes")->nullable();
            $table->decimal("moisture_level", 5, 2)->nullable();
            $table->integer("nitrogen_level")->nullable();
            $table->integer("phosphorus_level")->nullable();
            $table->integer("potassium_level")->nullable();
            $table->text("observations")->nullable();
            $table->string("weather_condition")->nullable();
            $table->decimal("temperature", 5, 2)->nullable();
            $table->decimal("humidity", 5, 2)->nullable();
            $table->string("recorded_by")->nullable();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->timestamps();

            $table->foreign("crop_id")->references("id")->on("crops")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->index("crop_id");
            $table->index("health_status");
            $table->index("created_at");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("crop_health_records");
    }
};
