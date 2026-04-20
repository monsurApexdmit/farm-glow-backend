<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("inventory_items", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("farm_id");
            $table->unsignedBigInteger("category_id");
            $table->unsignedBigInteger("supplier_id")->nullable();
            $table->string("name");
            $table->string("sku")->unique();
            $table->text("description")->nullable();
            $table->string("unit");
            $table->decimal("quantity", 10, 3)->default(0);
            $table->decimal("min_quantity", 10, 3)->nullable();
            $table->decimal("max_quantity", 10, 3)->nullable();
            $table->decimal("cost_per_unit", 10, 2);
            $table->decimal("total_value", 10, 2)->default(0);
            $table->date("expiry_date")->nullable();
            $table->string("location")->nullable();
            $table->string("status")->default("active");
            $table->boolean("is_active")->default(true);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("deleted_by")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("farm_id")->references("id")->on("farms")->onDelete("cascade");
            $table->foreign("category_id")->references("id")->on("inventory_categories")->onDelete("restrict");
            $table->foreign("supplier_id")->references("id")->on("inventory_suppliers")->onDelete("set null");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->foreign("deleted_by")->references("id")->on("users")->onDelete("set null");
            $table->index("farm_id");
            $table->index("category_id");
            $table->index("status");
            $table->index("is_active");
        });
    }
    public function down(): void { Schema::dropIfExists("inventory_items"); }
};
