<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("livestock_breeding_records", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("farm_id");
            $table->unsignedBigInteger("male_id");
            $table->unsignedBigInteger("female_id");
            $table->date("breeding_date");
            $table->date("expected_birth_date")->nullable();
            $table->date("actual_birth_date")->nullable();
            $table->integer("offspring_count")->nullable();
            $table->text("observations")->nullable();
            $table->string("status")->default("planned");
            $table->unsignedBigInteger("created_by")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("farm_id")->references("id")->on("farms")->onDelete("cascade");
            $table->foreign("male_id")->references("id")->on("livestocks")->onDelete("cascade");
            $table->foreign("female_id")->references("id")->on("livestocks")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->index("farm_id");
            $table->index("status");
            $table->index("breeding_date");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("livestock_breeding_records");
    }
};
