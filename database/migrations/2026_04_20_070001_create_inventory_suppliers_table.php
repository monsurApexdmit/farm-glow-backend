<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("inventory_suppliers", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("company_id");
            $table->string("name");
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->string("address")->nullable();
            $table->string("city")->nullable();
            $table->string("state")->nullable();
            $table->string("country")->nullable();
            $table->string("website")->nullable();
            $table->text("notes")->nullable();
            $table->boolean("is_active")->default(true);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("deleted_by")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("company_id")->references("id")->on("companies")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->foreign("deleted_by")->references("id")->on("users")->onDelete("set null");
            $table->index("company_id");
            $table->index("is_active");
        });
    }
    public function down(): void { Schema::dropIfExists("inventory_suppliers"); }
};
