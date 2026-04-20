<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("inventory_categories", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug")->nullable();
            $table->text("description")->nullable();
            $table->string("icon")->nullable();
            $table->string("color")->nullable();
            $table->boolean("is_active")->default(true);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->index("is_active");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("inventory_categories");
    }
};
