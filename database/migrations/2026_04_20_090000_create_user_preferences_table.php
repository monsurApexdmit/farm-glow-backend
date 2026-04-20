<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("user_preferences", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->string("theme")->default("light");
            $table->string("language")->default("en");
            $table->string("timezone")->default("UTC");
            $table->boolean("notifications_enabled")->default(true);
            $table->boolean("email_notifications")->default(true);
            $table->json("data")->nullable();
            $table->timestamps();
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
        });
    }
    public function down(): void { Schema::dropIfExists("user_preferences"); }
};
