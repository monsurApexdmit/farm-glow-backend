<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("crops", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("field_id");
            $table->string("name");
            $table->string("type");
            $table->text("description")->nullable();
            $table->string("variety")->nullable();
            $table->timestamp("planting_date");
            $table->timestamp("expected_harvest_date")->nullable();
            $table->timestamp("actual_harvest_date")->nullable();
            $table->decimal("estimated_yield", 10, 2)->nullable();
            $table->decimal("actual_yield", 10, 2)->nullable();
            $table->string("yield_unit")->default("kg");
            $table->string("status")->default("planning");
            $table->boolean("is_active")->default(true);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("deleted_by")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("field_id")->references("id")->on("fields")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->foreign("deleted_by")->references("id")->on("users")->onDelete("set null");
            $table->index("field_id");
            $table->index("status");
            $table->index("is_active");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("crops");
    }
};
