<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("financial_reports", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("farm_id");
            $table->string("type");
            $table->integer("month")->nullable();
            $table->integer("year");
            $table->decimal("total_income", 15, 2)->default(0);
            $table->decimal("total_expenses", 15, 2)->default(0);
            $table->decimal("net_profit", 15, 2)->default(0);
            $table->json("data")->nullable();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->timestamps();

            $table->foreign("farm_id")->references("id")->on("farms")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->index("farm_id");
            $table->index("type");
        });
    }
    public function down(): void { Schema::dropIfExists("financial_reports"); }
};
