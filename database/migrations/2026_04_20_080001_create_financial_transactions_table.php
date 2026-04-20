<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("financial_transactions", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("account_id");
            $table->unsignedBigInteger("farm_id")->nullable();
            $table->string("type");
            $table->string("category");
            $table->text("description")->nullable();
            $table->decimal("amount", 15, 2);
            $table->string("reference_number")->nullable();
            $table->date("transaction_date");
            $table->string("status")->default("completed");
            $table->text("notes")->nullable();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("deleted_by")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("account_id")->references("id")->on("financial_accounts")->onDelete("cascade");
            $table->foreign("farm_id")->references("id")->on("farms")->onDelete("set null");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->foreign("deleted_by")->references("id")->on("users")->onDelete("set null");
            $table->index("account_id");
            $table->index("type");
            $table->index("category");
            $table->index("transaction_date");
        });
    }
    public function down(): void { Schema::dropIfExists("financial_transactions"); }
};
