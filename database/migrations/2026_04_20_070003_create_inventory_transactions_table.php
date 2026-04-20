<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("inventory_transactions", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("inventory_item_id");
            $table->string("type");
            $table->decimal("quantity", 10, 3);
            $table->decimal("quantity_before", 10, 3);
            $table->decimal("quantity_after", 10, 3);
            $table->decimal("cost_per_unit", 10, 2)->nullable();
            $table->text("notes")->nullable();
            $table->string("reference_number")->nullable();
            $table->date("transaction_date");
            $table->unsignedBigInteger("created_by")->nullable();
            $table->timestamps();

            $table->foreign("inventory_item_id")->references("id")->on("inventory_items")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->index("inventory_item_id");
            $table->index("type");
            $table->index("transaction_date");
        });
    }
    public function down(): void { Schema::dropIfExists("inventory_transactions"); }
};
