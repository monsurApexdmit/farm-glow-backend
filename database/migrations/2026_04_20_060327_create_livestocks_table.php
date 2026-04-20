<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("livestocks", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("farm_id");
            $table->unsignedBigInteger("livestock_type_id");
            $table->unsignedBigInteger("shed_id")->nullable();
            $table->string("tag_number")->unique();
            $table->string("name")->nullable();
            $table->text("description")->nullable();
            $table->string("breed")->nullable();
            $table->string("gender")->nullable();
            $table->date("date_of_birth")->nullable();
            $table->date("acquisition_date")->nullable();
            $table->decimal("weight", 8, 2)->nullable();
            $table->string("weight_unit")->default("kg");
            $table->string("status")->default("active");
            $table->boolean("is_active")->default(true);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("deleted_by")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("farm_id")->references("id")->on("farms")->onDelete("cascade");
            $table->foreign("livestock_type_id")->references("id")->on("livestock_types")->onDelete("restrict");
            $table->foreign("shed_id")->references("id")->on("livestock_sheds")->onDelete("set null");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->foreign("deleted_by")->references("id")->on("users")->onDelete("set null");
            $table->index("farm_id");
            $table->index("tag_number");
            $table->index("status");
            $table->index("is_active");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("livestocks");
    }
};
