<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("livestock_health_records", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("livestock_id");
            $table->string("health_status");
            $table->text("observations")->nullable();
            $table->text("treatment")->nullable();
            $table->string("disease_name")->nullable();
            $table->date("disease_start_date")->nullable();
            $table->date("recovery_date")->nullable();
            $table->decimal("temperature", 5, 2)->nullable();
            $table->decimal("weight", 8, 2)->nullable();
            $table->string("weight_unit")->default("kg");
            $table->text("notes")->nullable();
            $table->string("veterinarian")->nullable();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->timestamps();

            $table->foreign("livestock_id")->references("id")->on("livestocks")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->index("livestock_id");
            $table->index("health_status");
            $table->index("created_at");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("livestock_health_records");
    }
};
