<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("financial_accounts", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("company_id");
            $table->string("name");
            $table->string("type");
            $table->text("description")->nullable();
            $table->decimal("opening_balance", 15, 2)->default(0);
            $table->decimal("current_balance", 15, 2)->default(0);
            $table->string("currency")->default("USD");
            $table->boolean("is_active")->default(true);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("deleted_by")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("company_id")->references("id")->on("companies")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->foreign("deleted_by")->references("id")->on("users")->onDelete("set null");
            $table->index("company_id");
            $table->index("type");
            $table->index("is_active");
        });
    }
    public function down(): void { Schema::dropIfExists("financial_accounts"); }
};
