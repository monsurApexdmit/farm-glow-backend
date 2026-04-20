<?php
namespace App\Http\Requests\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class RecordTransactionRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "inventory_item_id" => "required|exists:inventory_items,id",
            "type" => "nullable|in:use,restock,adjustment,loss",
            "quantity" => "required|numeric|min:0.001",
            "cost_per_unit" => "nullable|numeric|min:0",
            "notes" => "nullable|string",
            "reference_number" => "nullable|string",
            "transaction_date" => "required|date",
        ];
    }
}
