<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("worker_schedules", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("farm_id");
            $table->unsignedBigInteger("worker_id");
            $table->date("work_date");
            $table->time("start_time");
            $table->time("end_time");
            $table->string("shift_type");
            $table->text("notes")->nullable();
            $table->string("status")->default("scheduled");
            $table->unsignedBigInteger("created_by")->nullable();
            $table->timestamps();

            $table->foreign("farm_id")->references("id")->on("farms")->onDelete("cascade");
            $table->foreign("worker_id")->references("id")->on("workers")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->index("farm_id");
            $table->index("worker_id");
            $table->index("work_date");
            $table->index("status");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("worker_schedules");
    }
};
