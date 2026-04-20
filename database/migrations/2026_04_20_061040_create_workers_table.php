<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("workers", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("farm_id");
            $table->string("first_name");
            $table->string("last_name");
            $table->string("email")->unique();
            $table->string("phone")->nullable();
            $table->string("position");
            $table->string("employment_type");
            $table->string("national_id")->nullable();
            $table->date("date_of_birth")->nullable();
            $table->string("address")->nullable();
            $table->string("city")->nullable();
            $table->string("state")->nullable();
            $table->string("postal_code")->nullable();
            $table->string("emergency_contact_name")->nullable();
            $table->string("emergency_contact_phone")->nullable();
            $table->decimal("hourly_rate", 10, 2)->nullable();
            $table->decimal("monthly_salary", 12, 2)->nullable();
            $table->date("hiring_date");
            $table->date("termination_date")->nullable();
            $table->string("status")->default("active");
            $table->boolean("is_active")->default(true);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("deleted_by")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("farm_id")->references("id")->on("farms")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->foreign("deleted_by")->references("id")->on("users")->onDelete("set null");
            $table->index("farm_id");
            $table->index("status");
            $table->index("is_active");
            $table->unique(["farm_id", "email"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("workers");
    }
};
