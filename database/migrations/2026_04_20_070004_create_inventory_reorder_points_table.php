<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("inventory_reorder_points", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("inventory_item_id");
            $table->decimal("reorder_point", 10, 3);
            $table->decimal("reorder_quantity", 10, 3);
            $table->boolean("is_active")->default(true);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->timestamps();

            $table->foreign("inventory_item_id")->references("id")->on("inventory_items")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->unique("inventory_item_id");
        });
    }
    public function down(): void { Schema::dropIfExists("inventory_reorder_points"); }
};
