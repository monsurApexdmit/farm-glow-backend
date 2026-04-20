<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("worker_attendances", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("worker_id");
            $table->date("attendance_date");
            $table->time("check_in_time")->nullable();
            $table->time("check_out_time")->nullable();
            $table->decimal("hours_worked", 5, 2)->nullable();
            $table->string("status");
            $table->text("notes")->nullable();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->timestamps();

            $table->foreign("worker_id")->references("id")->on("workers")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->index("worker_id");
            $table->index("attendance_date");
            $table->index("status");
            $table->unique(["worker_id", "attendance_date"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("worker_attendances");
    }
};
