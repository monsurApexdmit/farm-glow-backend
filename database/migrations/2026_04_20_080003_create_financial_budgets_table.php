<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("financial_budgets", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("farm_id");
            $table->string("category");
            $table->decimal("budgeted_amount", 15, 2);
            $table->decimal("spent_amount", 15, 2)->default(0);
            $table->integer("month");
            $table->integer("year");
            $table->text("notes")->nullable();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->timestamps();

            $table->foreign("farm_id")->references("id")->on("farms")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->unique(["farm_id", "category", "month", "year"]);
            $table->index("farm_id");
        });
    }
    public function down(): void { Schema::dropIfExists("financial_budgets"); }
};
