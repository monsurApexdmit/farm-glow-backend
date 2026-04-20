<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("livestock_types", function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->string("code")->unique();
            $table->text("description")->nullable();
            $table->string("icon")->nullable();
            $table->string("color")->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("livestock_types");
    }
};
