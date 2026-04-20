<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("financial_invoices", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("farm_id");
            $table->string("invoice_number")->unique();
            $table->string("client_name");
            $table->string("client_email")->nullable();
            $table->text("description")->nullable();
            $table->decimal("amount", 15, 2);
            $table->date("issue_date");
            $table->date("due_date");
            $table->date("paid_date")->nullable();
            $table->string("status")->default("pending");
            $table->text("notes")->nullable();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("deleted_by")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("farm_id")->references("id")->on("farms")->onDelete("cascade");
            $table->foreign("created_by")->references("id")->on("users")->onDelete("set null");
            $table->foreign("deleted_by")->references("id")->on("users")->onDelete("set null");
            $table->index("farm_id");
            $table->index("status");
            $table->index("due_date");
        });
    }
    public function down(): void { Schema::dropIfExists("financial_invoices"); }
};
