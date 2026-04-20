<?php
namespace App\Http\Requests\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryItemRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "farm_id" => "sometimes|exists:farms,id",
            "category_id" => "sometimes|exists:inventory_categories,id",
            "supplier_id" => "nullable|exists:inventory_suppliers,id",
            "name" => "sometimes|string|max:255",
            "sku" => "sometimes|string|unique:inventory_items,sku," . $this->route('inventory.id'),
            "description" => "nullable|string",
            "unit" => "sometimes|string",
            "quantity" => "nullable|numeric|min:0",
            "min_quantity" => "nullable|numeric|min:0",
            "max_quantity" => "nullable|numeric|min:0",
            "cost_per_unit" => "sometimes|numeric|min:0",
            "expiry_date" => "nullable|date",
            "location" => "nullable|string",
        ];
    }
}
