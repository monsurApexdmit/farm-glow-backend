<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("livestock_sheds", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("farm_id");
            $table->string("name");
            $table->text("description")->nullable();
            $table->string("shed_type");
            $table->integer("capacity")->nullable();
            $table->decimal("length", 8, 2)->nullable();
            $table->decimal("width", 8, 2)->nullable();
            $table->decimal("area", 10, 2)->nullable();
            $table->decimal("temperature_min", 5, 2)->nullable();
            $table->decimal("temperature_max", 5, 2)->nullable();
            $table->decimal("humidity_level", 5, 2)->nullable();
            $table->timestamp("last_cleaned_at")->nullable();
            $table->string("status")->default("operational");
            $table->boolean("is_active")->default(true);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("deleted_by")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("farm_id")->references("id")->on("farms")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->foreign("deleted_by")->references("id")->on("users")->onDelete("set null");
            $table->index("farm_id");
            $table->index("status");
            $table->index("is_active");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("livestock_sheds");
    }
};
