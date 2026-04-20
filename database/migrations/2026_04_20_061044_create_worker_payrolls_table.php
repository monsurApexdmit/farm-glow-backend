<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("worker_payrolls", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("worker_id");
            $table->year("year");
            $table->unsignedTinyInteger("month");
            $table->decimal("base_salary", 12, 2)->nullable();
            $table->decimal("hours_worked", 8, 2)->nullable();
            $table->decimal("hourly_rate", 10, 2)->nullable();
            $table->decimal("overtime_hours", 8, 2)->nullable();
            $table->decimal("overtime_amount", 12, 2)->nullable();
            $table->decimal("bonuses", 12, 2)->nullable();
            $table->decimal("deductions", 12, 2)->nullable();
            $table->decimal("net_salary", 12, 2);
            $table->date("payment_date")->nullable();
            $table->string("payment_method")->nullable();
            $table->string("payment_status")->default("pending");
            $table->text("notes")->nullable();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->timestamps();

            $table->foreign("worker_id")->references("id")->on("workers")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->index("worker_id");
            $table->index("year");
            $table->index("month");
            $table->index("payment_status");
            $table->unique(["worker_id", "year", "month"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("worker_payrolls");
    }
};
