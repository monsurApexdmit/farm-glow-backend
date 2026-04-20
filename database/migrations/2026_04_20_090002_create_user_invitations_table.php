<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("user_invitations", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("company_id");
            $table->string("email")->unique();
            $table->string("token")->unique();
            $table->string("role")->default("farmer");
            $table->dateTime("expires_at");
            $table->dateTime("accepted_at")->nullable();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->timestamps();
            $table->foreign("company_id")->references("id")->on("companies")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
        });
    }
    public function down(): void { Schema::dropIfExists("user_invitations"); }
};
