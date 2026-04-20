<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("worker_performances", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("worker_id");
            $table->date("review_date");
            $table->integer("overall_rating");
            $table->integer("quality_rating")->nullable();
            $table->integer("productivity_rating")->nullable();
            $table->integer("attitude_rating")->nullable();
            $table->integer("reliability_rating")->nullable();
            $table->text("comments")->nullable();
            $table->text("strengths")->nullable();
            $table->text("improvements")->nullable();
            $table->string("reviewed_by")->nullable();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->timestamps();

            $table->foreign("worker_id")->references("id")->on("workers")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->index("worker_id");
            $table->index("review_date");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("worker_performances");
    }
};
